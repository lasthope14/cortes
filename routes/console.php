<?php

use App\Services\Imports\ContractWorkbookImporter;
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

Artisan::command('contract:import {path}', function (ContractWorkbookImporter $importer, string $path) {
    $project = $importer->import($path);

    $this->info("Proyecto importado: {$project->name}");
    $this->line("Contrato: ".($project->contract_number ?? 'sin numero'));
    $this->line("Plantillas registradas: {$project->excelTemplates()->count()}");
    $this->line("Lineas contractuales registradas: {$project->contractLines()->count()}");
})->purpose('Importa la caratula contractual y registra sus lineas en base de datos');
