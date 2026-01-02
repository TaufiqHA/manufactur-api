<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SubAssembly;
use App\Models\ProjectItem;
use App\Models\Material;
use App\Models\User;

class SubAssemblyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_can_get_sub_assemblies_list(): void
    {
        SubAssembly::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/sub-assemblies');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'qty_per_parent',
                                 'total_needed',
                                 'completed_qty',
                                 'total_produced',
                                 'consumed_qty',
                                 'is_locked',
                                 'created_at',
                                 'updated_at'
                             ]
                         ]
                     ]
                 ]);
    }

    public function test_can_create_sub_assembly(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $material = Material::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'name' => 'Test Sub Assembly',
            'qty_per_parent' => 2,
            'total_needed' => 10,
            'completed_qty' => 0,
            'total_produced' => 0,
            'consumed_qty' => 0,
            'material_id' => $material->id,
            'processes' => json_encode([
                [
                    'name' => 'Assembly',
                    'duration' => 60,
                    'status' => 'pending',
                ]
            ]),
            'step_stats' => json_encode([
                'total_steps' => 5,
                'completed_steps' => 0,
                'pending_steps' => 5,
            ]),
            'is_locked' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sub-assemblies', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Test Sub Assembly',
                         'qty_per_parent' => 2,
                         'total_needed' => 10,
                         'is_locked' => false,
                     ]
                 ]);

        $this->assertDatabaseHas('sub_assemblies', [
            'name' => 'Test Sub Assembly',
            'qty_per_parent' => 2,
            'total_needed' => 10,
            'is_locked' => false,
        ]);
    }

    public function test_can_show_sub_assembly(): void
    {
        $subAssembly = SubAssembly::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/sub-assemblies/{$subAssembly->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $subAssembly->id,
                         'name' => $subAssembly->name,
                     ]
                 ]);
    }

    public function test_can_update_sub_assembly(): void
    {
        $subAssembly = SubAssembly::factory()->create();
        $newMaterial = Material::factory()->create();

        $data = [
            'name' => 'Updated Sub Assembly',
            'qty_per_parent' => 5,
            'material_id' => $newMaterial->id,
            'is_locked' => true,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/sub-assemblies/{$subAssembly->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Updated Sub Assembly',
                         'qty_per_parent' => 5,
                         'is_locked' => true,
                     ]
                 ]);

        $this->assertDatabaseHas('sub_assemblies', [
            'id' => $subAssembly->id,
            'name' => 'Updated Sub Assembly',
            'qty_per_parent' => 5,
            'is_locked' => true,
        ]);
    }

    public function test_can_delete_sub_assembly(): void
    {
        $subAssembly = SubAssembly::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/sub-assemblies/{$subAssembly->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Sub assembly deleted successfully'
                 ]);

        $this->assertDatabaseMissing('sub_assemblies', [
            'id' => $subAssembly->id,
        ]);
    }

    public function test_validation_fails_when_creating_sub_assembly_without_required_fields(): void
    {
        $data = [
            'name' => '', // Required field missing
            'qty_per_parent' => -1, // Invalid value
            'processes' => json_encode([
                [
                    'name' => 'Assembly',
                    'duration' => 60,
                    'status' => 'pending',
                ]
            ]),
            'step_stats' => json_encode([
                'total_steps' => 5,
                'completed_steps' => 0,
                'pending_steps' => 5,
            ]),
            'is_locked' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/sub-assemblies', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed',
                 ]);
    }

    public function test_unauthorized_user_cannot_access_sub_assembly_endpoints(): void
    {
        $subAssembly = SubAssembly::factory()->create();

        $response = $this->getJson('/api/sub-assemblies');
        $response->assertStatus(401);

        $response = $this->postJson('/api/sub-assemblies', []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/sub-assemblies/{$subAssembly->id}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/sub-assemblies/{$subAssembly->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/sub-assemblies/{$subAssembly->id}");
        $response->assertStatus(401);
    }
}
