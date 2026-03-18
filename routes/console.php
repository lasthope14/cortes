<?php

use App\Services\Imports\WorkbookStructureAnalyzer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('workbook:analyze {path}', function (WorkbookStructureAnalyzer $analyzer, string $path) {
    $summary = $analyzer->analyze($path);

    $this->table(
        ['Hoja', 'Filas', 'Columnas', 'Filas con datos'],
        array_map(static fn (array $sheet): array => [
            $sheet['title'],
            $sheet['rows'],
            $sheet['columns'],
            $sheet['non_empty_rows'],
        ], $summary)
    );
})->purpose('Analiza la estructura basica de un workbook Excel');
