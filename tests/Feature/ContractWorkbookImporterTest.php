<?php

namespace Tests\Feature;

use App\Enums\ContractLineType;
use App\Enums\ExcelTemplateType;
use App\Models\ContractLine;
use App\Models\ExcelTemplate;
use App\Models\Project;
use App\Services\Imports\ContractWorkbookImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ContractWorkbookImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_cover_sheet_and_contract_lines(): void
    {
        $path = storage_path('framework/testing/test-contract-import.xlsx');
        $spreadsheet = new Spreadsheet();
        $cover = $spreadsheet->getActiveSheet();
        $cover->setTitle('Carátula de Estimaciones');

        $cover->setCellValue('F14', 'Proyecto de prueba');
        $cover->setCellValue('F16', 'CT-001');
        $cover->setCellValue('F18', 'HIDROOBRAS S.A.');
        $cover->setCellValue('O14', 'COP');
        $cover->setCellValue('R14', 0.15);
        $cover->setCellValue('V14', 0.25);
        $cover->setCellValue('V16', 0.1);
        $cover->setCellValue('J22', '2026-01-22');
        $cover->setCellValue('O22', '2027-01-22');
        $cover->setCellValue('U20', '2026-03-04');
        $cover->setCellValue('F22', '01 (UNO)');

        $cover->setCellValue('A27', '1.1');
        $cover->setCellValue('D27', 1);
        $cover->setCellValue('E27', '24');
        $cover->setCellValue('F27', 'INSTALACIONES HIDROSANITARIAS');

        $cover->setCellValue('A28', '1.2');
        $cover->setCellValue('D28', 2);
        $cover->setCellValue('E28', '0');
        $cover->setCellValue('F28', 'PABELLONES Y GUARDIA INTERNA');

        $cover->setCellValue('A29', '1.3');
        $cover->setCellValue('D29', 3);
        $cover->setCellValue('E29', '24.2');
        $cover->setCellValue('F29', 'RED AGUA POTABLE');

        $cover->setCellValue('A30', '1.4');
        $cover->setCellValue('D30', 4);
        $cover->setCellValue('E30', '24.2.3.3.12');
        $cover->setCellValue('F30', "CODO 90 CXC PVC-S Ø2''");
        $cover->setCellValue('G30', 'UND');
        $cover->setCellValue('H30', 427);
        $cover->setCellValue('I30', 12023.64);
        $cover->setCellValue('J30', 5134094.28);

        $cover->setCellValue('I31', 'TOTAL');

        $memory = $spreadsheet->createSheet();
        $memory->setTitle('24.2.3.3.12');
        $memory->setCellValue('D8', 'CALLE 72 # 104 - 98');
        $memory->setCellValue('D11', 'SAN CRISTOBAL - MEDELLIN');

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        $project = app(ContractWorkbookImporter::class)->import($path);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertSame('Proyecto de prueba', $project->name);
        $this->assertSame('CT-001', $project->contract_number);
        $this->assertSame('SAN CRISTOBAL - MEDELLIN', $project->location);
        $this->assertSame('COP', $project->currency);
        $this->assertCount(2, ExcelTemplate::all());
        $this->assertCount(4, ContractLine::orderBy('display_order')->get());

        $lines = ContractLine::orderBy('display_order')->get()->values();

        $this->assertSame(ContractLineType::Group, $lines[0]->row_type);
        $this->assertNull($lines[0]->parent_id);
        $this->assertSame(ContractLineType::Group, $lines[1]->row_type);
        $this->assertSame($lines[0]->id, $lines[1]->parent_id);
        $this->assertSame(ContractLineType::Group, $lines[2]->row_type);
        $this->assertSame($lines[1]->id, $lines[2]->parent_id);
        $this->assertSame(ContractLineType::Item, $lines[3]->row_type);
        $this->assertSame($lines[2]->id, $lines[3]->parent_id);
        $this->assertSame(
            ExcelTemplateType::QuantityMemory,
            $lines[3]->excelTemplate->template_type
        );
        $this->assertSame('24.2.3.3.12', $lines[3]->metadata['memory_sheet_title']);
    }
}
