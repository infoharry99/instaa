<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeployTest extends TestCase
{
    public function test_unauthorized_deploy_access_is_forbidden(): void
    {
        $response = $this->get('/deploy');
        $response->assertStatus(403);
    }

    public function test_authorized_deploy_status_returns_diagnostic_info(): void
    {
        $response = $this->get('/deploy?key=instalead2026');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'php_version',
            'laravel_version',
            'database_status',
            'available_commands',
        ]);
    }

    public function test_clear_cache_route_runs_successfully(): void
    {
        $response = $this->get('/deploy/clear-cache?key=instalead2026');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}
