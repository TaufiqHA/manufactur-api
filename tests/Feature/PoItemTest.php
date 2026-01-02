<?php

namespace Tests\Feature;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PoItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to get all PoItems.
     */
    public function test_can_get_all_po_items(): void
    {
        // Create authenticated user
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Create some PoItems using factory
        $poItems = PoItem::factory()->count(3)->create();

        $response = $this->getJson('/api/po-items');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => $poItems->toArray()
                 ]);
    }

    /**
     * Test to get a single PoItem.
     */
    public function test_can_get_single_po_item(): void
    {
        // Create authenticated user
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Create a PoItem using factory
        $poItem = PoItem::factory()->create();

        $response = $this->getJson("/api/po-items/{$poItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => $poItem->toArray()
                 ]);
    }

    /**
     * Test to create a new PoItem.
     */
    public function test_can_create_po_item(): void
    {
        // Create authenticated user
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Create related models
        $purchaseOrder = PurchaseOrder::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'po_id' => $purchaseOrder->id,
            'material_id' => $material->id,
            'name' => 'Test Po Item',
            'qty' => 10,
            'price' => 100.50
        ];

        $response = $this->postJson('/api/po-items', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => $data,
                     'message' => 'PoItem created successfully'
                 ]);

        $this->assertDatabaseHas('po_items', $data);
    }

    /**
     * Test to update a PoItem.
     */
    public function test_can_update_po_item(): void
    {
        // Create authenticated user
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Create a PoItem using factory
        $poItem = PoItem::factory()->create();

        $updatedData = [
            'po_id' => $poItem->po_id,
            'material_id' => $poItem->material_id,
            'name' => 'Updated Po Item',
            'qty' => 20,
            'price' => 200.75
        ];

        $response = $this->putJson("/api/po-items/{$poItem->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => $updatedData,
                     'message' => 'PoItem updated successfully'
                 ]);

        $this->assertDatabaseHas('po_items', $updatedData);
    }

    /**
     * Test to delete a PoItem.
     */
    public function test_can_delete_po_item(): void
    {
        // Create authenticated user
        $user = $this->createUser();
        $this->actingAs($user, 'sanctum');

        // Create a PoItem using factory
        $poItem = PoItem::factory()->create();

        $response = $this->deleteJson("/api/po-items/{$poItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'PoItem deleted successfully'
                 ]);

        $this->assertDatabaseMissing('po_items', ['id' => $poItem->id]);
    }

    /**
     * Helper method to create a user for testing.
     */
    private function createUser()
    {
        return \App\Models\User::factory()->create();
    }
}
