<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RfqItem;
use App\Models\Rfq;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RfqItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test to get all RFQ items.
     */
    public function test_can_get_all_rfq_items(): void
    {
        $user = $this->createUser();
        
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();
        
        $rfqItem = RfqItem::factory()->create([
            'rfq_id' => $rfq->id,
            'material_id' => $material->id
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/rfq-items');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'rfq_id',
                    'material_id',
                    'name',
                    'qty',
                    'price',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test to get a single RFQ item.
     */
    public function test_can_get_single_rfq_item(): void
    {
        $user = $this->createUser();
        
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();
        
        $rfqItem = RfqItem::factory()->create([
            'rfq_id' => $rfq->id,
            'material_id' => $material->id
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/rfq-items/{$rfqItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $rfqItem->id,
                'rfq_id' => $rfqItem->rfq_id,
                'material_id' => $rfqItem->material_id,
                'name' => $rfqItem->name,
                'qty' => $rfqItem->qty,
                'price' => $rfqItem->price
            ]);
    }

    /**
     * Test to create a new RFQ item.
     */
    public function test_can_create_rfq_item(): void
    {
        $user = $this->createUser();
        
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'rfq_id' => $rfq->id,
            'material_id' => $material->id,
            'name' => 'Test RFQ Item',
            'qty' => 10,
            'price' => 100.50
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/rfq-items', $data);

        $response->assertStatus(201)
            ->assertJson([
                'rfq_id' => $data['rfq_id'],
                'material_id' => $data['material_id'],
                'name' => $data['name'],
                'qty' => $data['qty'],
                'price' => $data['price']
            ]);

        $this->assertDatabaseHas('rfq_items', $data);
    }

    /**
     * Test validation for creating RFQ item.
     */
    public function test_validation_for_creating_rfq_item(): void
    {
        $user = $this->createUser();

        $data = [
            'name' => '', // Invalid: empty name
            'qty' => -5,  // Invalid: negative quantity
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/rfq-items', $data);

        $response->assertStatus(422);

        // Check that the response has errors structure
        $response->assertJsonStructure([
            'errors'
        ]);
    }

    /**
     * Test to update an existing RFQ item.
     */
    public function test_can_update_rfq_item(): void
    {
        $user = $this->createUser();
        
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();
        
        $rfqItem = RfqItem::factory()->create([
            'rfq_id' => $rfq->id,
            'material_id' => $material->id
        ]);

        $updatedData = [
            'name' => 'Updated RFQ Item',
            'qty' => 20,
            'price' => 200.75
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/rfq-items/{$rfqItem->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $rfqItem->id,
                'name' => $updatedData['name'],
                'qty' => $updatedData['qty'],
                'price' => $updatedData['price']
            ]);

        $this->assertDatabaseHas('rfq_items', [
            'id' => $rfqItem->id,
            'name' => $updatedData['name'],
            'qty' => $updatedData['qty'],
            'price' => $updatedData['price']
        ]);
    }

    /**
     * Test to delete an RFQ item.
     */
    public function test_can_delete_rfq_item(): void
    {
        $user = $this->createUser();
        
        $rfq = Rfq::factory()->create();
        $material = Material::factory()->create();
        
        $rfqItem = RfqItem::factory()->create([
            'rfq_id' => $rfq->id,
            'material_id' => $material->id
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/rfq-items/{$rfqItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('rfq_items', [
            'id' => $rfqItem->id
        ]);
    }

    /**
     * Test unauthenticated access to RFQ items.
     */
    public function test_unauthenticated_user_cannot_access_rfq_items(): void
    {
        $response = $this->getJson('/api/rfq-items');
        $response->assertStatus(401);

        $rfqItem = RfqItem::factory()->create();
        $response = $this->postJson('/api/rfq-items', []);
        $response->assertStatus(401);

        $response = $this->putJson("/api/rfq-items/{$rfqItem->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/rfq-items/{$rfqItem->id}");
        $response->assertStatus(401);
    }

    /**
     * Helper method to create a user for testing.
     */
    private function createUser(): \App\Models\User
    {
        return \App\Models\User::factory()->create();
    }
}