<?php

namespace App\Services\Imports;

use App\Models\ExcelTemplate;
use App\Models\Project;
use RuntimeException;

class ContractWorkbookImporter
{
    public function import(Project $project, ExcelTemplate $template): void
    {
        if (! is_file($template->storage_path)) {
            throw new RuntimeException("No se encontro la plantilla en {$template->storage_path}");
        }

        // Esta clase usara PhpSpreadsheet/Maatwebsite para:
        // 1. leer la hoja "Caratula de Estimaciones",
        // 2. crear contract_lines preservando excel_row y display_order,
        // 3. identificar filas de grupo vs item,
        // 4. registrar hojas de memoria como plantillas por partida.
        //
        // Primera entrega: la estructura de datos ya existe.
        // La siguiente iteracion aterriza el parser fila por fila.
    }
}
