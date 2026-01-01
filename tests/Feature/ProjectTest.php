<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\User;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_projects(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_project(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => 'PROJ-TEST001',
            'name' => 'Test Project',
            'customer' => 'Test Customer',
            'start_date' => '2024-01-01',
            'deadline' => '2024-12-31',
            'status' => 'PLANNED',
            'progress' => 0,
            'qty_per_unit' => 10,
            'procurement_qty' => 100,
            'total_qty' => 1000,
            'unit' => 'unit',
            'is_locked' => false,
        ];

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'code' => 'PROJ-TEST001',
                     'name' => 'Test Project',
                     'customer' => 'Test Customer',
                     'status' => 'PLANNED',
                     'progress' => 0,
                     'qty_per_unit' => 10,
                     'procurement_qty' => 100,
                     'total_qty' => 1000,
                     'unit' => 'unit',
                     'is_locked' => false,
                 ]);

        $this->assertDatabaseHas('projects', [
            'code' => 'PROJ-TEST001',
            'name' => 'Test Project',
            'customer' => 'Test Customer',
        ]);
    }

    public function test_can_show_single_project(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $project = Project::factory()->create();

        $response = $this->getJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $project->id,
                     'code' => $project->code,
                     'name' => $project->name,
                 ]);
    }

    public function test_can_update_project(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $project = Project::factory()->create();

        $data = [
            'code' => 'PROJ-UPDATED',
            'name' => 'Updated Project Name',
            'customer' => 'Updated Customer',
            'start_date' => '2024-02-01',
            'deadline' => '2024-11-30',
            'status' => 'IN_PROGRESS',
            'progress' => 25,
            'qty_per_unit' => 20,
            'procurement_qty' => 200,
            'total_qty' => 2000,
            'unit' => 'pcs',
            'is_locked' => true,
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'code' => 'PROJ-UPDATED',
                     'name' => 'Updated Project Name',
                     'customer' => 'Updated Customer',
                     'status' => 'IN_PROGRESS',
                     'progress' => 25,
                 ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'code' => 'PROJ-UPDATED',
            'name' => 'Updated Project Name',
            'customer' => 'Updated Customer',
        ]);
    }

    public function test_can_delete_project(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $project = Project::factory()->create();

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Project deleted successfully'
                 ]);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_validation_fails_for_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => '', // Required field
            'name' => '', // Required field
            'customer' => '', // Required field
            'start_date' => 'invalid-date', // Invalid date
            'deadline' => 'invalid-date', // Invalid date
            'status' => 'INVALID_STATUS', // Invalid status
            'progress' => 150, // Out of range
            'qty_per_unit' => -1, // Invalid value
            'procurement_qty' => -1, // Invalid value
            'total_qty' => -1, // Invalid value
            'unit' => '', // Required field
            'is_locked' => 'not-boolean', // Invalid boolean
        ];

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors'
                 ]);
    }

    public function test_unauthorized_access_to_projects(): void
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }
}
