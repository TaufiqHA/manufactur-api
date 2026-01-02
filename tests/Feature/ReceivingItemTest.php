<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ReceivingItem;
use App\Models\ReceivingGood;
use App\Models\Material;
use App\Models\User;

class ReceivingItemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_receiving_items(): void
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/receiving-items');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'receiving_id',
                             'material_id',
                             'name',
                             'qty',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }

    public function test_can_get_single_receiving_item(): void
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson("/api/receiving-items/{$receivingItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $receivingItem->id,
                     'name' => $receivingItem->name,
                     'qty' => $receivingItem->qty,
                 ]);
    }

    public function test_can_create_receiving_item(): void
    {
        $receivingGood = ReceivingGood::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Test Receiving Item',
            'qty' => 10,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/receiving-items', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Receiving item created successfully.',
                     'data' => [
                         'name' => $data['name'],
                         'qty' => $data['qty'],
                         'receiving_id' => $data['receiving_id'],
                         'material_id' => $data['material_id'],
                     ]
                 ]);

        $this->assertDatabaseHas('receiving_items', [
            'name' => $data['name'],
            'qty' => $data['qty'],
            'receiving_id' => $data['receiving_id'],
            'material_id' => $data['material_id'],
        ]);
    }

    public function test_cannot_create_receiving_item_with_invalid_data(): void
    {
        $data = [
            'receiving_id' => null,
            'material_id' => null,
            'name' => '',
            'qty' => -5,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/receiving-items', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed',
                 ]);
    }

    public function test_can_update_receiving_item(): void
    {
        $receivingItem = ReceivingItem::factory()->create();
        $receivingGood = ReceivingGood::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'receiving_id' => $receivingGood->id,
            'material_id' => $material->id,
            'name' => 'Updated Receiving Item',
            'qty' => 25,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/receiving-items/{$receivingItem->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Receiving item updated successfully.',
                     'data' => [
                         'name' => $data['name'],
                         'qty' => $data['qty'],
                     ]
                 ]);

        $this->assertDatabaseHas('receiving_items', [
            'id' => $receivingItem->id,
            'name' => $data['name'],
            'qty' => $data['qty'],
        ]);
    }

    public function test_cannot_update_receiving_item_with_invalid_data(): void
    {
        $receivingItem = ReceivingItem::factory()->create();

        $data = [
            'receiving_id' => null,
            'material_id' => null,
            'name' => '',
            'qty' => -5,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/receiving-items/{$receivingItem->id}", $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed',
                 ]);
    }

    public function test_can_delete_receiving_item(): void
    {
        $receivingItem = ReceivingItem::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/receiving-items/{$receivingItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Receiving item deleted successfully.'
                 ]);

        $this->assertDatabaseMissing('receiving_items', [
            'id' => $receivingItem->id,
        ]);
    }

    public function test_can_get_nonexistent_receiving_item_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/receiving-items/99999');

        $response->assertStatus(404);
    }

    public function test_can_delete_nonexistent_receiving_item_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson('/api/receiving-items/99999');

        $response->assertStatus(404);
    }
}
