<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BomItem;
use App\Models\ProjectItem;
use App\Models\Material;

class BomItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate for the tests
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    /**
     * Test listing all BOM items
     */
    public function test_can_list_bom_items(): void
    {
        // Create some BOM items
        $bomItems = BomItem::factory()->count(3)->create();

        $response = $this->getJson('/api/bom-items');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test showing a specific BOM item
     */
    public function test_can_show_bom_item(): void
    {
        $bomItem = BomItem::factory()->create();

        $response = $this->getJson("/api/bom-items/{$bomItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $bomItem->id,
                     'item_id' => $bomItem->item_id,
                     'material_id' => $bomItem->material_id,
                     'quantity_per_unit' => $bomItem->quantity_per_unit,
                     'total_required' => $bomItem->total_required,
                     'allocated' => $bomItem->allocated,
                     'realized' => $bomItem->realized,
                 ]);
    }

    /**
     * Test creating a new BOM item
     */
    public function test_can_create_bom_item(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'material_id' => $material->id,
            'quantity_per_unit' => 5,
            'total_required' => 100,
            'allocated' => 50,
            'realized' => 30,
        ];

        $response = $this->postJson('/api/bom-items', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('bom_items', [
            'item_id' => $data['item_id'],
            'material_id' => $data['material_id'],
            'quantity_per_unit' => $data['quantity_per_unit'],
            'total_required' => $data['total_required'],
            'allocated' => $data['allocated'],
            'realized' => $data['realized'],
        ]);
    }

    /**
     * Test creating a BOM item with invalid data
     */
    public function test_cannot_create_bom_item_with_invalid_data(): void
    {
        $data = [
            'item_id' => null,
            'material_id' => null,
            'quantity_per_unit' => -1,
            'total_required' => -1,
        ];

        $response = $this->postJson('/api/bom-items', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'error',
                     'messages'
                 ])
                 ->assertJson([
                     'error' => 'Validation failed',
                 ]);
    }

    /**
     * Test updating a BOM item
     */
    public function test_can_update_bom_item(): void
    {
        $bomItem = BomItem::factory()->create();
        $projectItem = ProjectItem::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'material_id' => $material->id,
            'quantity_per_unit' => 10,
            'total_required' => 200,
            'allocated' => 100,
            'realized' => 60,
        ];

        $response = $this->putJson("/api/bom-items/{$bomItem->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('bom_items', [
            'id' => $bomItem->id,
            'item_id' => $data['item_id'],
            'material_id' => $data['material_id'],
            'quantity_per_unit' => $data['quantity_per_unit'],
            'total_required' => $data['total_required'],
            'allocated' => $data['allocated'],
            'realized' => $data['realized'],
        ]);
    }

    /**
     * Test updating a BOM item with invalid data
     */
    public function test_cannot_update_bom_item_with_invalid_data(): void
    {
        $bomItem = BomItem::factory()->create();

        $data = [
            'item_id' => null,
            'material_id' => null,
            'quantity_per_unit' => -1,
            'total_required' => -1,
        ];

        $response = $this->putJson("/api/bom-items/{$bomItem->id}", $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'error',
                     'messages'
                 ])
                 ->assertJson([
                     'error' => 'Validation failed',
                 ]);
    }

    /**
     * Test deleting a BOM item
     */
    public function test_can_delete_bom_item(): void
    {
        $bomItem = BomItem::factory()->create();

        $response = $this->deleteJson("/api/bom-items/{$bomItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('bom_items', [
            'id' => $bomItem->id,
        ]);
    }

    /**
     * Test BOM item includes related data
     */
    public function test_bom_item_includes_related_data(): void
    {
        $bomItem = BomItem::factory()->create();

        $response = $this->getJson("/api/bom-items/{$bomItem->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'item_id',
                     'material_id',
                     'quantity_per_unit',
                     'total_required',
                     'allocated',
                     'realized',
                     'created_at',
                     'updated_at',
                     'item',
                     'material'
                 ]);
    }

    /**
     * Test accessing non-existent BOM item
     */
    public function test_cannot_access_non_existent_bom_item(): void
    {
        $response = $this->getJson('/api/bom-items/99999');

        // This will likely return a 404 or 500 depending on how Laravel handles missing resources
        // If using Laravel's default implicit route model binding, it would return 404
        $response->assertStatus(404);
    }
}