<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;

class ProjectItemTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_project_items(): void
    {
        $this->actingAs($this->user, 'sanctum');

        ProjectItem::factory()->count(3)->create();

        $response = $this->getJson('/api/project-items');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'
            ]);
    }

    public function test_can_get_single_project_item(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $projectItem = ProjectItem::factory()->create();

        $response = $this->getJson("/api/project-items/{$projectItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $projectItem->id
            ]);
    }

    public function test_can_create_project_item(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $project = Project::factory()->create();

        $data = [
            'project_id' => $project->id,
            'name' => 'Test Project Item',
            'dimensions' => '10x20 cm',
            'thickness' => '5 mm',
            'qty_set' => 2,
            'quantity' => 10,
            'unit' => 'pcs',
            'is_bom_locked' => false,
            'is_workflow_locked' => false,
            'flow_type' => 'NEW',
            'warehouse_qty' => 5,
            'shipped_qty' => 0,
        ];

        $response = $this->postJson('/api/project-items', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Project item created successfully.',
                'data' => [
                    'name' => 'Test Project Item',
                    'dimensions' => '10x20 cm',
                    'thickness' => '5 mm',
                    'qty_set' => 2,
                    'quantity' => 10,
                    'unit' => 'pcs',
                    'is_bom_locked' => false,
                    'is_workflow_locked' => false,
                    'flow_type' => 'NEW',
                    'warehouse_qty' => 5,
                    'shipped_qty' => 0,
                ]
            ]);

        $this->assertDatabaseHas('project_items', [
            'name' => 'Test Project Item',
            'dimensions' => '10x20 cm',
            'thickness' => '5 mm',
            'qty_set' => 2,
            'quantity' => 10,
            'unit' => 'pcs',
            'is_bom_locked' => false,
            'is_workflow_locked' => false,
            'flow_type' => 'NEW',
            'warehouse_qty' => 5,
            'shipped_qty' => 0,
        ]);
    }

    public function test_can_update_project_item(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $projectItem = ProjectItem::factory()->create();
        $project = Project::factory()->create();

        $data = [
            'project_id' => $project->id,
            'name' => 'Updated Project Item',
            'dimensions' => '15x25 cm',
            'thickness' => '10 mm',
            'qty_set' => 3,
            'quantity' => 15,
            'unit' => 'set',
            'is_bom_locked' => true,
            'is_workflow_locked' => true,
            'flow_type' => 'OLD',
            'warehouse_qty' => 8,
            'shipped_qty' => 2,
        ];

        $response = $this->putJson("/api/project-items/{$projectItem->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Project item updated successfully.',
                'data' => [
                    'name' => 'Updated Project Item',
                    'dimensions' => '15x25 cm',
                    'thickness' => '10 mm',
                    'qty_set' => 3,
                    'quantity' => 15,
                    'unit' => 'set',
                    'is_bom_locked' => true,
                    'is_workflow_locked' => true,
                    'flow_type' => 'OLD',
                    'warehouse_qty' => 8,
                    'shipped_qty' => 2,
                ]
            ]);

        $this->assertDatabaseHas('project_items', [
            'id' => $projectItem->id,
            'name' => 'Updated Project Item',
            'dimensions' => '15x25 cm',
            'thickness' => '10 mm',
            'qty_set' => 3,
            'quantity' => 15,
            'unit' => 'set',
            'is_bom_locked' => true,
            'is_workflow_locked' => true,
            'flow_type' => 'OLD',
            'warehouse_qty' => 8,
            'shipped_qty' => 2,
        ]);
    }

    public function test_can_delete_project_item(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $projectItem = ProjectItem::factory()->create();

        $response = $this->deleteJson("/api/project-items/{$projectItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Project item deleted successfully.'
            ]);

        $this->assertDatabaseMissing('project_items', [
            'id' => $projectItem->id
        ]);
    }

    public function test_validation_fails_when_creating_with_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'name' => '', // Required field
            'qty_set' => -1, // Should be >= 0
            'quantity' => -1, // Should be >= 0
            'flow_type' => 'INVALID', // Should be OLD or NEW
        ];

        $response = $this->postJson('/api/project-items', $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed'
            ]);
    }

    public function test_validation_fails_when_updating_with_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $projectItem = ProjectItem::factory()->create();

        $data = [
            'name' => '', // Required field
            'qty_set' => -1, // Should be >= 0
            'quantity' => -1, // Should be >= 0
            'flow_type' => 'INVALID', // Should be OLD or NEW
        ];

        $response = $this->putJson("/api/project-items/{$projectItem->id}", $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Validation failed'
            ]);
    }

    public function test_unauthorized_user_cannot_access_project_items(): void
    {
        $response = $this->getJson('/api/project-items');

        $response->assertStatus(401);
    }
}
