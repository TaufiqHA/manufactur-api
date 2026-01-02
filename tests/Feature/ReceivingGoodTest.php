<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\ReceivingGood;
use App\Models\PurchaseOrder;
use App\Models\User;

class ReceivingGoodTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_receiving_goods_list(): void
    {
        $this->actingAs($this->user, 'sanctum');

        ReceivingGood::factory()->count(3)->create();

        $response = $this->getJson('/api/receiving-goods');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data',
            'links',
            'current_page',
            'last_page',
            'per_page',
            'total'
        ]);
    }

    public function test_can_get_single_receiving_good(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $receivingGood = ReceivingGood::factory()->create();

        $response = $this->getJson("/api/receiving-goods/{$receivingGood->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $receivingGood->id,
                'code' => $receivingGood->code,
                'po_id' => $receivingGood->po_id,
            ])
            ->assertJsonFragment([
                'date' => $receivingGood->date->format('Y-m-d')
            ]);
    }

    public function test_can_create_receiving_good(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $purchaseOrder = PurchaseOrder::factory()->create();

        $data = [
            'code' => 'RG-TEST123',
            'date' => '2023-12-01',
            'po_id' => $purchaseOrder->id,
        ];

        $response = $this->postJson('/api/receiving-goods', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good created successfully.',
                'data' => [
                    'code' => $data['code'],
                    'date' => $data['date'],
                    'po_id' => $data['po_id'],
                ]
            ]);

        $this->assertDatabaseHas('receiving_goods', [
            'code' => $data['code'],
            'date' => $data['date'] . ' 00:00:00', // Laravel stores dates with time component
            'po_id' => $data['po_id'],
        ]);
    }

    public function test_cannot_create_receiving_good_with_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => '', // Invalid: empty
            'date' => 'invalid-date', // Invalid: not a date
            'po_id' => 999, // Invalid: doesn't exist
        ];

        $response = $this->postJson('/api/receiving-goods', $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ]);
    }

    public function test_can_update_receiving_good(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $receivingGood = ReceivingGood::factory()->create();
        $purchaseOrder = PurchaseOrder::factory()->create();

        $data = [
            'code' => 'RG-UPDATED123',
            'date' => '2023-12-15',
            'po_id' => $purchaseOrder->id,
        ];

        $response = $this->putJson("/api/receiving-goods/{$receivingGood->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good updated successfully.',
                'data' => [
                    'code' => $data['code'],
                    'date' => $data['date'],
                    'po_id' => $data['po_id'],
                ]
            ]);

        $this->assertDatabaseHas('receiving_goods', [
            'id' => $receivingGood->id,
            'code' => $data['code'],
            'date' => $data['date'] . ' 00:00:00', // Laravel stores dates with time component
            'po_id' => $data['po_id'],
        ]);
    }

    public function test_cannot_update_receiving_good_with_invalid_data(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $receivingGood = ReceivingGood::factory()->create();

        $data = [
            'code' => '', // Invalid: empty
            'date' => 'invalid-date', // Invalid: not a date
            'po_id' => 999, // Invalid: doesn't exist
        ];

        $response = $this->putJson("/api/receiving-goods/{$receivingGood->id}", $data);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ]);
    }

    public function test_can_delete_receiving_good(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $receivingGood = ReceivingGood::factory()->create();

        $response = $this->deleteJson("/api/receiving-goods/{$receivingGood->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Receiving Good deleted successfully.'
            ]);

        $this->assertDatabaseMissing('receiving_goods', [
            'id' => $receivingGood->id,
        ]);
    }

    public function test_unauthorized_user_cannot_access_receiving_good_endpoints(): void
    {
        // Test index without authentication
        $response = $this->getJson('/api/receiving-goods');
        $response->assertStatus(401);

        // Test store without authentication
        $response = $this->postJson('/api/receiving-goods', []);
        $response->assertStatus(401);

        // Test show without authentication
        $receivingGood = ReceivingGood::factory()->create();
        $response = $this->getJson("/api/receiving-goods/{$receivingGood->id}");
        $response->assertStatus(401);

        // Test update without authentication
        $response = $this->putJson("/api/receiving-goods/{$receivingGood->id}", []);
        $response->assertStatus(401);

        // Test destroy without authentication
        $response = $this->deleteJson("/api/receiving-goods/{$receivingGood->id}");
        $response->assertStatus(401);
    }
}
