<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class MaterialTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_get_all_materials()
    {
        $this->actingAs($this->user, 'sanctum');

        Material::factory()->count(3)->create();

        $response = $this->getJson('/api/materials');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_get_a_single_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $response = $this->getJson("/api/materials/{$material->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $material->id,
                     'code' => $material->code,
                     'name' => $material->name,
                     'unit' => $material->unit,
                     'current_stock' => $material->current_stock,
                     'safety_stock' => $material->safety_stock,
                     'price_per_unit' => $material->price_per_unit,
                     'category' => $material->category,
                 ]);
    }

    /** @test */
    public function it_can_create_a_new_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'code' => 'MAT-TEST001',
            'name' => 'Test Material',
            'unit' => 'pcs',
            'current_stock' => 100,
            'safety_stock' => 10,
            'price_per_unit' => 15.50,
            'category' => 'RAW',
        ];

        $response = $this->postJson('/api/materials', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'code' => $data['code'],
                     'name' => $data['name'],
                     'unit' => $data['unit'],
                     'current_stock' => $data['current_stock'],
                     'safety_stock' => $data['safety_stock'],
                     'price_per_unit' => $data['price_per_unit'],
                     'category' => $data['category'],
                 ]);

        $this->assertDatabaseHas('materials', [
            'code' => $data['code'],
            'name' => $data['name'],
            'unit' => $data['unit'],
            'current_stock' => $data['current_stock'],
            'safety_stock' => $data['safety_stock'],
            'price_per_unit' => $data['price_per_unit'],
            'category' => $data['category'],
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/materials', []);

        $response->assertStatus(422)
                 ->assertJson([
                     'errors' => [
                         'code' => ['The code field is required.'],
                         'name' => ['The name field is required.'],
                         'unit' => ['The unit field is required.'],
                         'current_stock' => ['The current stock field is required.'],
                         'safety_stock' => ['The safety stock field is required.'],
                         'price_per_unit' => ['The price per unit field is required.'],
                         'category' => ['The category field is required.'],
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_an_existing_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $data = [
            'code' => 'MAT-UPDATED001',
            'name' => 'Updated Material',
            'unit' => 'kg',
            'current_stock' => 200,
            'safety_stock' => 20,
            'price_per_unit' => 25.75,
            'category' => 'FINISHING',
        ];

        $response = $this->putJson("/api/materials/{$material->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $material->id,
                     'code' => $data['code'],
                     'name' => $data['name'],
                     'unit' => $data['unit'],
                     'current_stock' => $data['current_stock'],
                     'safety_stock' => $data['safety_stock'],
                     'price_per_unit' => $data['price_per_unit'],
                     'category' => $data['category'],
                 ]);

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'code' => $data['code'],
            'name' => $data['name'],
            'unit' => $data['unit'],
            'current_stock' => $data['current_stock'],
            'safety_stock' => $data['safety_stock'],
            'price_per_unit' => $data['price_per_unit'],
            'category' => $data['category'],
        ]);
    }

    /** @test */
    public function it_can_delete_a_material()
    {
        $this->actingAs($this->user, 'sanctum');

        $material = Material::factory()->create();

        $response = $this->deleteJson("/api/materials/{$material->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('materials', [
            'id' => $material->id,
        ]);
    }

    /** @test */
    public function it_requires_authentication_for_material_routes()
    {
        $response = $this->getJson('/api/materials');

        $response->assertStatus(401); // Unauthorized status when token is missing
    }
}