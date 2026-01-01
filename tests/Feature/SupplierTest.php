<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_all_suppliers(): void
    {
        $this->actingAs($this->user, 'sanctum');

        Supplier::factory()->count(3)->create();

        $response = $this->getJson('/api/suppliers');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'name' => 'Test Supplier',
            'address' => '123 Test Street, Test City',
            'contact' => '081234567890',
        ];

        $response = $this->postJson('/api/suppliers', $data);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $data['name'],
                'address' => $data['address'],
                'contact' => $data['contact'],
            ]);

        $this->assertDatabaseHas('suppliers', $data);
    }

    /** @test */
    public function it_validates_supplier_creation(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'name' => '', // Required field
            'address' => '', // Required field
            'contact' => '', // Required field
        ];

        $response = $this->postJson('/api/suppliers', $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'address' => ['The address field is required.'],
                    'contact' => ['The contact field is required.'],
                ]
            ]);
    }

    /** @test */
    public function it_can_show_a_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $supplier->id,
                'name' => $supplier->name,
                'address' => $supplier->address,
                'contact' => $supplier->contact,
            ]);
    }

    /** @test */
    public function it_can_update_a_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $data = [
            'name' => 'Updated Supplier Name',
            'address' => '456 Updated Street, Updated City',
            'contact' => '089876543210',
        ];

        $response = $this->putJson("/api/suppliers/{$supplier->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $supplier->id,
                'name' => $data['name'],
                'address' => $data['address'],
                'contact' => $data['contact'],
            ]);

        $this->assertDatabaseHas('suppliers', $data);
    }

    /** @test */
    public function it_validates_supplier_update(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $data = [
            'name' => '', // Required field
            'address' => '', // Required field
            'contact' => '', // Required field
        ];

        $response = $this->putJson("/api/suppliers/{$supplier->id}", $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'address' => ['The address field is required.'],
                    'contact' => ['The contact field is required.'],
                ]
            ]);
    }

    /** @test */
    public function it_can_delete_a_supplier(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }
}