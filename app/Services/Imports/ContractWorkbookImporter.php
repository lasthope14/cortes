<?php

namespace App\Services\Imports;

use App\Enums\ContractLineType;
use App\Enums\ExcelTemplateType;
use App\Models\ContractLine;
use App\Models\ExcelTemplate;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class ContractWorkbookImporter
{
    public function import(string $path): Project
    {
        if (! is_file($path)) {
            throw new RuntimeException("No se encontro el archivo {$path}");
        }

        $workbook = IOFactory::load($path);
        $coverSheet = $this->resolveCoverSheet($workbook->getAllSheets());
        $memorySheets = [];

        foreach ($workbook->getWorksheetIterator() as $sheet) {
            if ($sheet->getTitle() !== $coverSheet->getTitle()) {
                $memorySheets[] = $sheet;
            }
        }

        $projectPayload = $this->extractProjectPayload($coverSheet, $memorySheets, $path);

        return DB::transaction(function () use ($coverSheet, $memorySheets, $path, $projectPayload) {
            $project = $this->upsertProject($projectPayload);

            $coverTemplate = ExcelTemplate::create([
                'project_id' => $project->id,
                'name' => basename($path),
                'template_type' => ExcelTemplateType::EstimateCover,
                'sheet_name' => $coverSheet->getTitle(),
                'storage_path' => $path,
                'version' => '1',
                'is_active' => true,
                'notes' => 'Plantilla principal importada desde workbook contractual.',
            ]);

            $memoryTemplateMap = [];

            foreach ($memorySheets as $sheet) {
                $template = ExcelTemplate::create([
                    'project_id' => $project->id,
                    'name' => basename($path).' :: '.$sheet->getTitle(),
                    'template_type' => ExcelTemplateType::QuantityMemory,
                    'sheet_name' => $sheet->getTitle(),
                    'storage_path' => $path,
                    'version' => '1',
                    'is_active' => true,
                    'notes' => 'Hoja ejemplo de memoria por partida encontrada en el workbook.',
                ]);

                $memoryTemplateMap[$sheet->getTitle()] = $template;
            }

            $this->importContractLines($project, $coverSheet, $coverTemplate, $memoryTemplateMap);

            return $project->fresh(['excelTemplates', 'contractLines']);
        });
    }

    /**
     * @param  array<int, Worksheet>  $worksheets
     */
    private function resolveCoverSheet(array $worksheets): Worksheet
    {
        foreach ($worksheets as $sheet) {
            $title = Str::lower(Str::ascii($sheet->getTitle()));

            if (Str::contains($title, 'caratula')) {
                return $sheet;
            }
        }

        if ($worksheets === []) {
            throw new RuntimeException('El workbook no contiene hojas para importar.');
        }

        return $worksheets[0];
    }

    /**
     * @param  array<int, Worksheet>  $memorySheets
     * @return array<string, mixed>
     */
    private function extractProjectPayload(Worksheet $coverSheet, array $memorySheets, string $path): array
    {
        $name = $this->cellToString($coverSheet->getCell('F14'));
        $contractNumber = $this->cellToString($coverSheet->getCell('F16'));
        $contractor = $this->cellToString($coverSheet->getCell('F18'));
        $currency = $this->cellToString($coverSheet->getCell('O14')) ?: 'COP';
        $location = $this->extractLocationFromMemorySheets($memorySheets, 'D11');
        $metadata = [
            'source_workbook' => basename($path),
            'source_path' => $path,
            'cover_sheet' => $coverSheet->getTitle(),
            'memory_sheet_titles' => array_map(static fn (Worksheet $sheet): string => $sheet->getTitle(), $memorySheets),
            'estimate_number_example' => $this->cellToString($coverSheet->getCell('F22')),
            'estimate_prepared_at' => $this->cellToDateString($coverSheet->getCell('U20')),
            'estimate_period_start' => $this->cellToDateString($coverSheet->getCell('T22')),
            'estimate_period_end' => $this->cellToDateString($coverSheet->getCell('V22')),
            'project_address' => $this->extractLocationFromMemorySheets($memorySheets, 'D8'),
            'work_location' => $location,
            'subcontractor' => $contractor,
        ];

        if ($name === null) {
            throw new RuntimeException('No fue posible identificar el nombre del proyecto en la caratula.');
        }

        return [
            'name' => $name,
            'client_name' => null,
            'contractor_name' => $contractor,
            'location' => $location,
            'contract_number' => $contractNumber,
            'currency' => $currency,
            'advance_percentage' => $this->cellToDecimal($coverSheet->getCell('R14')) ?? 0,
            'scheduled_progress' => $this->cellToDecimal($coverSheet->getCell('V14')),
            'actual_progress' => $this->cellToDecimal($coverSheet->getCell('V16')),
            'start_date' => $this->cellToDateString($coverSheet->getCell('J22')),
            'planned_end_date' => $this->cellToDateString($coverSheet->getCell('O22')),
            'metadata' => $metadata,
        ];
    }

    /**
     * @param  array<int, Worksheet>  $memorySheets
     */
    private function extractLocationFromMemorySheets(array $memorySheets, string $cell): ?string
    {
        foreach ($memorySheets as $sheet) {
            $value = $this->cellToString($sheet->getCell($cell));

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertProject(array $payload): Project
    {
        $project = Project::query()
            ->when(
                $payload['contract_number'] ?? null,
                fn ($query, $contractNumber) => $query->where('contract_number', $contractNumber),
                fn ($query) => $query->where('name', $payload['name'])
            )
            ->first();

        if ($project !== null) {
            if ($project->fieldReports()->exists() || $project->estimates()->exists()) {
                throw new RuntimeException(
                    'El proyecto ya tiene reportes de campo o cortes. La reimportacion automatica fue bloqueada para no romper trazabilidad.'
                );
            }

            $project->fill($payload)->save();
            $project->contractLines()->delete();
            $project->excelTemplates()->delete();

            return $project->fresh();
        }

        return Project::create($payload);
    }

    /**
     * @param  array<string, ExcelTemplate>  $memoryTemplateMap
     */
    private function importContractLines(
        Project $project,
        Worksheet $sheet,
        ExcelTemplate $coverTemplate,
        array $memoryTemplateMap
    ): void {
        $highestRow = $sheet->getHighestDataRow();
        $lastGroupId = null;
        $lastGroupCode = null;
        $groupIdsByCode = [];
        $displayOrder = 0;

        for ($row = 27; $row <= $highestRow; $row++) {
            if ($this->isTotalsRow($sheet, $row)) {
                break;
            }

            $description = $this->cellToString($sheet->getCell("F{$row}"));
            $conceptCode = $this->cellToString($sheet->getCell("E{$row}"));
            $costCenter = $this->cellToString($sheet->getCell("A{$row}"));
            $unit = $this->cellToString($sheet->getCell("G{$row}"));

            if ($description === null && $conceptCode === null && $costCenter === null) {
                continue;
            }

            $rowType = $unit !== null ? ContractLineType::Item : ContractLineType::Group;
            $displayOrder++;

            $template = $memoryTemplateMap[$conceptCode] ?? $coverTemplate;
            $parentId = $this->resolveParentId(
                $conceptCode,
                $rowType,
                $groupIdsByCode,
                $lastGroupId,
                $lastGroupCode
            );

            $line = ContractLine::create([
                'project_id' => $project->id,
                'excel_template_id' => $template->id,
                'parent_id' => $parentId,
                'display_order' => $displayOrder,
                'excel_row' => $row,
                'row_type' => $rowType,
                'cost_center' => $costCenter,
                'item_number' => $this->cellToString($sheet->getCell("D{$row}")),
                'concept_code' => $conceptCode,
                'description' => $description ?? 'Sin descripcion',
                'unit' => $unit,
                'budget_quantity' => $this->cellToDecimal($sheet->getCell("H{$row}")),
                'unit_price' => $this->cellToDecimal($sheet->getCell("I{$row}")),
                'budget_amount' => $this->cellToDecimal($sheet->getCell("J{$row}")),
                'allows_field_capture' => $rowType === ContractLineType::Item,
                'measurement_schema' => $this->buildMeasurementSchema($unit),
                'metadata' => [
                    'source_sheet' => $sheet->getTitle(),
                    'memory_sheet_title' => isset($memoryTemplateMap[$conceptCode]) ? $conceptCode : null,
                ],
            ]);

            if ($rowType === ContractLineType::Group) {
                $lastGroupId = $line->id;
                $lastGroupCode = $conceptCode;

                if ($conceptCode !== null) {
                    $groupIdsByCode[$conceptCode] = $line->id;
                }
            }
        }
    }

    private function isTotalsRow(Worksheet $sheet, int $row): bool
    {
        return $this->cellToString($sheet->getCell("I{$row}")) === 'TOTAL';
    }

    /**
     * @param  array<string, int>  $groupIdsByCode
     */
    private function resolveParentId(
        ?string $conceptCode,
        ContractLineType $rowType,
        array $groupIdsByCode,
        ?int $lastGroupId,
        ?string $lastGroupCode
    ): ?int {
        if ($lastGroupCode === '0' && $lastGroupId !== null) {
            return $lastGroupId;
        }

        if ($conceptCode !== null && $conceptCode !== '' && $conceptCode !== '0') {
            $segments = explode('.', $conceptCode);

            if ($rowType === ContractLineType::Group && count($segments) === 1) {
                return null;
            }

            while (count($segments) > 1) {
                array_pop($segments);
                $prefix = implode('.', $segments);

                if (isset($groupIdsByCode[$prefix])) {
                    return $groupIdsByCode[$prefix];
                }
            }
        }

        return $lastGroupId;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildMeasurementSchema(?string $unit): ?array
    {
        if ($unit === null) {
            return null;
        }

        $normalizedUnit = Str::upper($unit);
        $mode = match ($normalizedUnit) {
            'ML' => 'linear',
            'M3' => 'volume',
            default => 'count',
        };

        return [
            'mode' => $mode,
            'fields' => [
                'element',
                'axis',
                'level',
                'plan_reference',
                'length',
                'width',
                'height',
                'area',
                'volume',
                'weight',
                'quantity',
                'subtotal',
                'element_count',
                'observations',
            ],
        ];
    }

    private function cellToString(Cell $cell): ?string
    {
        $value = $cell->getCalculatedValue();

        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_numeric($value)) {
            $numeric = (float) $value;

            return fmod($numeric, 1.0) === 0.0
                ? (string) (int) $numeric
                : rtrim(rtrim(number_format($numeric, 8, '.', ''), '0'), '.');
        }

        $text = preg_replace('/\s+/u', ' ', trim((string) $value));

        return $text !== '' ? $text : null;
    }

    private function cellToDecimal(Cell $cell): ?float
    {
        $value = $cell->getCalculatedValue();

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return is_numeric((string) $value) ? (float) $value : null;
    }

    private function cellToDateString(Cell $cell): ?string
    {
        $value = $cell->getCalculatedValue();

        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->toDateString();
        }

        if (ExcelDate::isDateTime($cell) && is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->toDateString();
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
