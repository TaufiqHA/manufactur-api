<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MachineTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_machines(): void
    {
        $this->actingAs($this->user);

        Machine::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/machines');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_machine(): void
    {
        $this->actingAs($this->user);

        $data = [
            'user_id' => $this->user->id,
            'code' => 'MCH-TEST001',
            'name' => 'Test Machine',
            'type' => 'CNC',
            'capacity_per_hour' => 100,
            'status' => 'RUNNING',
            'is_maintenance' => false,
        ];

        $response = $this->postJson('/api/machines', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'code' => 'MCH-TEST001',
                     'name' => 'Test Machine',
                     'type' => 'CNC',
                     'capacity_per_hour' => 100,
                     'status' => 'RUNNING',
                     'is_maintenance' => false,
                 ]);

        $this->assertDatabaseHas('machines', [
            'code' => 'MCH-TEST001',
            'name' => 'Test Machine',
            'type' => 'CNC',
            'capacity_per_hour' => 100,
            'status' => 'RUNNING',
            'is_maintenance' => false,
        ]);
    }

    /** @test */
    public function it_can_show_a_machine(): void
    {
        $this->actingAs($this->user);

        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/machines/{$machine->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $machine->id,
                     'code' => $machine->code,
                     'name' => $machine->name,
                 ]);
    }

    /** @test */
    public function it_can_update_a_machine(): void
    {
        $this->actingAs($this->user);

        $machine = Machine::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Old Name',
            'status' => 'IDLE',
        ]);

        $data = [
            'name' => 'Updated Name',
            'status' => 'RUNNING',
        ];

        $response = $this->putJson("/api/machines/{$machine->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Updated Name',
                     'status' => 'RUNNING',
                 ]);

        $this->assertDatabaseHas('machines', [
            'id' => $machine->id,
            'name' => 'Updated Name',
            'status' => 'RUNNING',
        ]);
    }

    /** @test */
    public function it_can_delete_a_machine(): void
    {
        $this->actingAs($this->user);

        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/machines/{$machine->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('machines', [
            'id' => $machine->id,
        ]);
    }

    /** @test */
    public function it_validates_machine_data_on_create(): void
    {
        $this->actingAs($this->user);

        $data = [
            'user_id' => 999, // Non-existent user
            'code' => '', // Required field
            'name' => '', // Required field
            'type' => '', // Required field
            'capacity_per_hour' => -1, // Should be >= 0
            'status' => 'INVALID_STATUS', // Invalid status
            'is_maintenance' => 'not_boolean', // Should be boolean
        ];

        $response = $this->postJson('/api/machines', $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'user_id',
                         'code',
                         'name',
                         'type',
                         'capacity_per_hour',
                         'status',
                         'is_maintenance',
                     ]
                 ]);
    }

    /** @test */
    public function it_validates_machine_data_on_update(): void
    {
        $this->actingAs($this->user);

        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'user_id' => 999, // Non-existent user
            'code' => '', // Required field
            'capacity_per_hour' => -1, // Should be >= 0
            'status' => 'INVALID_STATUS', // Invalid status
        ];

        $response = $this->putJson("/api/machines/{$machine->id}", $data);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'message',
                     'errors' => [
                         'user_id',
                         'code',
                         'capacity_per_hour',
                         'status',
                     ]
                 ]);
    }

    /** @test */
    public function it_prevents_accessing_other_users_machines(): void
    {
        $this->actingAs($this->user);

        $otherUser = User::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/machines/{$machine->id}");
        $response->assertStatus(403);

        $response = $this->putJson("/api/machines/{$machine->id}", ['name' => 'Updated']);
        $response->assertStatus(403);

        $response = $this->deleteJson("/api/machines/{$machine->id}");
        $response->assertStatus(403);
    }
}
