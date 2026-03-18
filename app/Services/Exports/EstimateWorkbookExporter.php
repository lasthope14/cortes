<?php

namespace App\Services\Exports;

use App\Models\Estimate;

class EstimateWorkbookExporter
{
    public function export(Estimate $estimate): string
    {
        // Esta clase escribira sobre la plantilla original del cliente:
        // - caratula con acumulados previos, corte actual y faltantes,
        // - memorias por partida con todas las filas ejecutadas del item,
        // - referencias a evidencias y plano record cuando aplique.
        return '';
    }
}
