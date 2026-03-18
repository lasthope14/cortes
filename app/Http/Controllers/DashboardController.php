<?php

namespace App\Http\Controllers;

use App\Models\ContractLine;
use App\Models\Estimate;
use App\Models\FieldReport;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'stats' => [
                'projects' => Project::count(),
                'contract_lines' => ContractLine::count(),
                'field_reports' => FieldReport::count(),
                'estimates' => Estimate::count(),
            ],
            'milestones' => [
                'Importar la caratula del Excel preservando fila, orden y jerarquia.',
                'Capturar memorias de cantidades por partida con filas de desglose y dos evidencias.',
                'Asignar filas ejecutadas al corte por rango de fechas sin duplicarlas.',
                'Congelar snapshots del corte y exportar la caratula y memorias con la plantilla del cliente.',
            ],
        ]);
    }
}
