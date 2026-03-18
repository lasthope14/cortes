<?php

namespace App\Services\Imports;

use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class WorkbookStructureAnalyzer
{
    /**
     * @return array<int, array<string, int|string>>
     */
    public function analyze(string $path): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("No se encontro el archivo {$path}");
        }

        $workbook = IOFactory::load($path);
        $summary = [];

        foreach ($workbook->getWorksheetIterator() as $sheet) {
            $nonEmptyRows = 0;
            $highestDataColumn = $sheet->getHighestDataColumn();
            $highestDataRow = $sheet->getHighestDataRow();

            foreach ($sheet->getRowIterator() as $row) {
                $hasValue = false;

                foreach ($row->getCellIterator() as $cell) {
                    if ($cell->getValue() !== null && $cell->getValue() !== '') {
                        $hasValue = true;
                        break;
                    }
                }

                if ($hasValue) {
                    $nonEmptyRows++;
                }
            }

            $summary[] = [
                'title' => $sheet->getTitle(),
                'rows' => $highestDataRow,
                'columns' => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestDataColumn),
                'non_empty_rows' => $nonEmptyRows,
            ];
        }

        return $summary;
    }
}
