<?php

namespace Tests\Feature;

use App\Models\Rfq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RfqTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_rfqs(): void
    {
        $this->actingAs($this->user);

        Rfq::factory()->count(3)->create();

        $response = $this->getJson('/api/rfqs');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_get_single_rfq(): void
    {
        $this->actingAs($this->user);

        $rfq = Rfq::factory()->create();

        $response = $this->getJson("/api/rfqs/{$rfq->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $rfq->id,
                     'code' => $rfq->code,
                     'description' => $rfq->description,
                     'status' => $rfq->status,
                 ]);
    }

    public function test_can_create_rfq(): void
    {
        $this->actingAs($this->user);

        $data = [
            'code' => 'RFQ-TEST-001',
            'date' => '2024-12-31',
            'description' => 'Test RFQ description',
            'status' => 'DRAFT',
        ];

        $response = $this->postJson('/api/rfqs', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rfqs', [
            'code' => 'RFQ-TEST-001',
            'date' => '2024-12-31 00:00:00',
            'description' => 'Test RFQ description',
            'status' => 'DRAFT',
        ]);
    }

    public function test_can_create_rfq_with_items(): void
    {
        $this->actingAs($this->user);

        // Create a material for testing
        $material = \App\Models\Material::factory()->create();

        $data = [
            'code' => 'RFQ-TEST-002',
            'date' => '2024-12-31',
            'description' => 'Test RFQ with items',
            'status' => 'DRAFT',
            'items' => [
                [
                    'material_id' => $material->id,
                    'name' => 'Test Item 1',
                    'qty' => 10,
                    'price' => 100.00,
                ],
                [
                    'material_id' => $material->id,
                    'name' => 'Test Item 2',
                    'qty' => 5,
                    'price' => 200.00,
                ],
            ],
        ];

        $response = $this->postJson('/api/rfqs', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('rfqs', [
            'code' => 'RFQ-TEST-002',
            'date' => '2024-12-31 00:00:00',
            'description' => 'Test RFQ with items',
            'status' => 'DRAFT',
        ]);

        $this->assertDatabaseHas('rfq_items', [
            'name' => 'Test Item 1',
            'qty' => 10,
            'price' => 100.00,
        ]);

        $this->assertDatabaseHas('rfq_items', [
            'name' => 'Test Item 2',
            'qty' => 5,
            'price' => 200.00,
        ]);
    }

    public function test_validation_required_for_creating_rfq(): void
    {
        $this->actingAs($this->user);

        $data = [
            'code' => '', // required
            'date' => '', // required
            'status' => '', // required
        ];

        $response = $this->postJson('/api/rfqs', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code', 'date', 'status']);
    }

    public function test_can_update_rfq(): void
    {
        $this->actingAs($this->user);

        $rfq = Rfq::factory()->create();
        $updatedData = [
            'code' => 'RFQ-UPDATED-001',
            'date' => '2025-01-15',
            'description' => 'Updated description',
            'status' => 'PO_CREATED',
        ];

        $response = $this->putJson("/api/rfqs/{$rfq->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('rfqs', [
            'id' => $rfq->id,
            'code' => 'RFQ-UPDATED-001',
            'date' => '2025-01-15 00:00:00',
            'description' => 'Updated description',
            'status' => 'PO_CREATED',
        ]);
    }

    public function test_validation_for_updating_rfq(): void
    {
        $this->actingAs($this->user);

        $rfq = Rfq::factory()->create();
        $invalidData = [
            'code' => '', // required when updating
            'date' => 'invalid-date', // should be valid date
            'status' => 'INVALID_STATUS', // should be DRAFT or PO_CREATED
        ];

        $response = $this->putJson("/api/rfqs/{$rfq->id}", $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code', 'date', 'status']);
    }

    public function test_can_delete_rfq(): void
    {
        $this->actingAs($this->user);

        $rfq = Rfq::factory()->create();

        $response = $this->deleteJson("/api/rfqs/{$rfq->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('rfqs', ['id' => $rfq->id]);
    }

    public function test_unauthorized_user_cannot_access_rfqs(): void
    {
        $response = $this->getJson('/api/rfqs');
        $response->assertStatus(401);

        $rfq = Rfq::factory()->create();
        $response = $this->getJson("/api/rfqs/{$rfq->id}");
        $response->assertStatus(401);

        $response = $this->postJson('/api/rfqs', []);
        $response->assertStatus(401);

        $response = $this->putJson("/api/rfqs/{$rfq->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/rfqs/{$rfq->id}");
        $response->assertStatus(401);
    }
}
