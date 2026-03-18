<?php

namespace Tests\Feature;

use App\Enums\ContractLineType;
use App\Enums\EvidenceType;
use App\Models\ContractLine;
use App\Models\Evidence;
use App\Models\Estimate;
use App\Models\FieldReport;
use App\Models\FieldReportRow;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_navigate_core_cutting_flow(): void
    {
        $engineer = User::factory()->create();

        $project = Project::create([
            'name' => 'Corte Medellin',
            'currency' => 'COP',
        ]);

        $contractLine = ContractLine::create([
            'project_id' => $project->id,
            'display_order' => 1,
            'row_type' => ContractLineType::Item,
            'concept_code' => '24.2.3.3.12',
            'description' => "CODO 90 CXC PVC-S 2",
            'unit' => 'UND',
            'budget_quantity' => 427,
            'unit_price' => 12023.64,
            'budget_amount' => 5134094.28,
        ]);

        $report = FieldReport::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'report_date' => '2026-03-18',
            'status' => 'draft',
        ]);

        $estimate = Estimate::create([
            'project_id' => $project->id,
            'created_by' => $engineer->id,
            'estimate_number' => '01',
            'period_start' => '2026-01-22',
            'period_end' => '2026-03-20',
            'status' => 'draft',
        ]);

        $row = FieldReportRow::create([
            'field_report_id' => $report->id,
            'contract_line_id' => $contractLine->id,
            'estimate_id' => $estimate->id,
            'captured_by' => $engineer->id,
            'work_date' => '2026-03-04',
            'sequence' => 1,
            'element' => 'Codo 90',
            'axis' => 'A-4',
            'level' => 'N1',
            'quantity' => 8,
            'subtotal' => 8,
            'element_count' => 8,
        ]);

        $evidence = Evidence::create([
            'field_report_row_id' => $row->id,
            'type' => EvidenceType::Work,
            'storage_disk' => 'public',
            'storage_path' => 'evidences/work/codo-90.jpg',
        ]);

        $this->assertTrue($project->contractLines->contains($contractLine));
        $this->assertTrue($project->fieldReports->contains($report));
        $this->assertTrue($project->estimates->contains($estimate));
        $this->assertTrue($contractLine->fieldReportRows->contains($row));
        $this->assertTrue($estimate->fieldReportRows->contains($row));
        $this->assertTrue($row->evidences->contains($evidence));
    }
}
