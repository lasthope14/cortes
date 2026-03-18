<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_loads(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Cortes de obra con memoria por partida.');
    }
}
