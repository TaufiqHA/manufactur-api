<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ItemStepConfigs;
use App\Models\ProjectItem;
use Illuminate\Support\Str;

class ItemStepConfigsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate
        $this->user = \App\Models\User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_can_get_all_item_step_configs(): void
    {
        // Create some test data
        $projectItem = ProjectItem::factory()->create();
        ItemStepConfigs::factory()->count(3)->create(['item_id' => $projectItem->id]);

        $response = $this->getJson('/api/item-step-configs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'item_id',
                        'step',
                        'sequence',
                        'created_at',
                        'updated_at',
                        'item' => [
                            'id',
                            'project_id',
                            'name',
                            'dimensions',
                            'thickness',
                            'qty_set',
                            'quantity',
                            'unit',
                            'is_bom_locked',
                            'is_workflow_locked',
                            'flow_type',
                            'warehouse_qty',
                            'shipped_qty',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]);
    }

    public function test_can_create_item_step_config(): void
    {
        $projectItem = ProjectItem::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'step' => 'Cutting',
            'sequence' => 1
        ];

        $response = $this->postJson('/api/item-step-configs', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Item step config created successfully',
                'data' => [
                    'item_id' => $data['item_id'],
                    'step' => $data['step'],
                    'sequence' => $data['sequence']
                ]
            ]);

        $this->assertDatabaseHas('item_step_configs', $data);
    }

    public function test_cannot_create_item_step_config_with_invalid_data(): void
    {
        $data = [
            'item_id' => 999999, // Non-existent item_id
            'step' => '',
            'sequence' => -1
        ];

        $response = $this->postJson('/api/item-step-configs', $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error'
            ]);
    }

    public function test_can_show_single_item_step_config(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $itemStepConfig = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);

        $response = $this->getJson("/api/item-step-configs/{$itemStepConfig->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $itemStepConfig->id,
                    'item_id' => $itemStepConfig->item_id,
                    'step' => $itemStepConfig->step,
                    'sequence' => $itemStepConfig->sequence
                ]
            ]);
    }

    public function test_can_update_item_step_config(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $itemStepConfig = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);

        $updatedData = [
            'item_id' => $projectItem->id,
            'step' => 'Milling',
            'sequence' => 2
        ];

        $response = $this->putJson("/api/item-step-configs/{$itemStepConfig->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item step config updated successfully',
                'data' => [
                    'id' => $itemStepConfig->id,
                    'item_id' => $updatedData['item_id'],
                    'step' => $updatedData['step'],
                    'sequence' => $updatedData['sequence']
                ]
            ]);

        $this->assertDatabaseHas('item_step_configs', $updatedData);
    }

    public function test_can_delete_item_step_config(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $itemStepConfig = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);

        $response = $this->deleteJson("/api/item-step-configs/{$itemStepConfig->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Item step config deleted successfully'
            ]);

        $this->assertDatabaseMissing('item_step_configs', ['id' => $itemStepConfig->id]);
    }

    public function test_can_get_step_configs_by_item_id(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $itemStepConfigs = ItemStepConfigs::factory()->count(3)->create(['item_id' => $projectItem->id]);

        $response = $this->getJson("/api/project-items/{$projectItem->id}/step-configs");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'item_id' => $projectItem->id
                    ]
                ]
            ]);
    }

    public function test_returns_404_when_getting_step_configs_for_nonexistent_item(): void
    {
        $response = $this->getJson('/api/project-items/999999/step-configs');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Project item not found'
            ]);
    }
}
