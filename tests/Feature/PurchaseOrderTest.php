<?php

namespace Tests\Feature;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Rfq;
use App\Models\PoItem;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate for protected routes
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_get_all_purchase_orders(): void
    {
        // Create some purchase orders with related items
        $purchaseOrders = PurchaseOrder::factory()->count(3)->create();
        foreach ($purchaseOrders as $purchaseOrder) {
            PoItem::factory()->count(2)->create(['po_id' => $purchaseOrder->id]);
        }

        $response = $this->getJson('/api/purchase-orders');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'current_page',
                     'data',
                     'first_page_url',
                     'from',
                     'last_page',
                     'last_page_url',
                     'links',
                     'next_page_url',
                     'path',
                     'per_page',
                     'prev_page_url',
                     'to',
                     'total'
                 ])
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'code',
                             'date',
                             'supplier_id',
                             'rfq_id',
                             'description',
                             'status',
                             'grand_total',
                             'items' => [
                                 '*' => [
                                     'id',
                                     'material_id',
                                     'name',
                                     'qty',
                                     'price',
                                     'po_id',
                                     'created_at',
                                     'updated_at'
                                 ]
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_get_single_purchase_order(): void
    {
        // Create a purchase order with related items
        $purchaseOrder = PurchaseOrder::factory()->create();
        $poItems = PoItem::factory()->count(2)->create(['po_id' => $purchaseOrder->id]);

        $response = $this->getJson("/api/purchase-orders/{$purchaseOrder->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $purchaseOrder->id,
                     'code' => $purchaseOrder->code,
                     'date' => $purchaseOrder->date->format('Y-m-d') . 'T00:00:00.000000Z',
                     'supplier_id' => $purchaseOrder->supplier_id,
                     'rfq_id' => $purchaseOrder->rfq_id,
                     'description' => $purchaseOrder->description,
                     'status' => $purchaseOrder->status,
                     'grand_total' => (string)$purchaseOrder->grand_total,
                 ])
                 ->assertJsonStructure([
                     'items' => [
                         '*' => [
                             'id',
                             'material_id',
                             'name',
                             'qty',
                             'price',
                             'po_id',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);
    }

    public function test_can_create_purchase_order_with_po_items(): void
    {
        // Create related models
        $supplier = Supplier::factory()->create();
        $rfq = Rfq::factory()->create();
        $material1 = Material::factory()->create();
        $material2 = Material::factory()->create();

        $data = [
            'code' => 'PO-001',
            'date' => '2023-12-01',
            'supplier_id' => $supplier->id,
            'rfq_id' => $rfq->id,
            'description' => 'Test purchase order',
            'status' => 'OPEN',
            'grand_total' => 1500.00,
            'po_items' => [
                [
                    'material_id' => $material1->id,
                    'name' => 'Material Item 1',
                    'qty' => 10,
                    'price' => 150.00
                ],
                [
                    'material_id' => $material2->id,
                    'name' => 'Material Item 2',
                    'qty' => 5,
                    'price' => 200.00
                ]
            ]
        ];

        $response = $this->postJson('/api/purchase-orders', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'code' => $data['code'],
                     'supplier_id' => $data['supplier_id'],
                     'rfq_id' => $data['rfq_id'],
                     'description' => $data['description'],
                     'status' => $data['status'],
                 ])
                 ->assertJsonPath('date', $data['date'] . 'T00:00:00.000000Z')
                 ->assertJsonPath('grand_total', sprintf('%.2f', $data['grand_total']))
                 ->assertJsonStructure([
                     'items' => [
                         '*' => [
                             'id',
                             'material_id',
                             'name',
                             'qty',
                             'price',
                             'po_id',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'code' => $data['code'],
            'date' => $data['date'] . ' 00:00:00',
            'supplier_id' => $data['supplier_id'],
            'rfq_id' => $data['rfq_id'],
            'description' => $data['description'],
            'status' => $data['status'],
            'grand_total' => $data['grand_total'],
        ]);

        // Check that PoItems were created
        $this->assertDatabaseCount('po_items', 2);
        $this->assertDatabaseHas('po_items', [
            'material_id' => $material1->id,
            'name' => 'Material Item 1',
            'qty' => 10,
            'price' => 150.00,
        ]);
        $this->assertDatabaseHas('po_items', [
            'material_id' => $material2->id,
            'name' => 'Material Item 2',
            'qty' => 5,
            'price' => 200.00,
        ]);
    }

    public function test_can_update_purchase_order(): void
    {
        // Create a purchase order and related models
        $purchaseOrder = PurchaseOrder::factory()->create();
        $poItems = PoItem::factory()->count(2)->create(['po_id' => $purchaseOrder->id]);
        $supplier = Supplier::factory()->create();
        $rfq = Rfq::factory()->create();

        $data = [
            'code' => 'PO-002',
            'date' => '2023-12-02',
            'supplier_id' => $supplier->id,
            'rfq_id' => $rfq->id,
            'description' => 'Updated purchase order',
            'status' => 'RECEIVED',
            'grand_total' => 2500.00,
        ];

        $response = $this->putJson("/api/purchase-orders/{$purchaseOrder->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $purchaseOrder->id,
                     'code' => $data['code'],
                     'supplier_id' => $data['supplier_id'],
                     'rfq_id' => $data['rfq_id'],
                     'description' => $data['description'],
                     'status' => $data['status'],
                 ])
                 ->assertJsonPath('date', $data['date'] . 'T00:00:00.000000Z')
                 ->assertJsonPath('grand_total', sprintf('%.2f', $data['grand_total']))
                 ->assertJsonStructure([
                     'items' => [
                         '*' => [
                             'id',
                             'material_id',
                             'name',
                             'qty',
                             'price',
                             'po_id',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'code' => $data['code'],
            'date' => $data['date'] . ' 00:00:00',
            'supplier_id' => $data['supplier_id'],
            'rfq_id' => $data['rfq_id'],
            'description' => $data['description'],
            'status' => $data['status'],
            'grand_total' => $data['grand_total'],
        ]);
    }

    public function test_can_delete_purchase_order(): void
    {
        // Create a purchase order
        $purchaseOrder = PurchaseOrder::factory()->create();

        $response = $this->deleteJson("/api/purchase-orders/{$purchaseOrder->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Purchase order deleted successfully'
                 ]);

        $this->assertDatabaseMissing('purchase_orders', [
            'id' => $purchaseOrder->id,
        ]);
    }

    public function test_validation_for_creating_purchase_order_with_po_items(): void
    {
        $response = $this->postJson('/api/purchase-orders', [
            'code' => 'PO-001',
            'date' => '2023-12-01',
            'supplier_id' => 1,
            'rfq_id' => 1,
            'description' => 'Test purchase order',
            'status' => 'OPEN',
            'grand_total' => 1500.00,
            'po_items' => [
                [
                    'material_id' => 999, // Non-existent material
                    'name' => 'Material Item 1',
                    'qty' => 10,
                    'price' => 150.00
                ]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['po_items.0.material_id']);
    }

    public function test_validation_for_updating_purchase_order(): void
    {
        $purchaseOrder = PurchaseOrder::factory()->create();

        $response = $this->putJson("/api/purchase-orders/{$purchaseOrder->id}", [
            'code' => '',
            'date' => '',
            'supplier_id' => '',
            'rfq_id' => '',
            'status' => '',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['code', 'date', 'supplier_id', 'rfq_id', 'status']);
    }
}
