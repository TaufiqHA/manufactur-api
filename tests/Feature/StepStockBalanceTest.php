<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\StepStockBalance;
use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfigs;
use App\Models\User;

class StepStockBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_step_stock_balances(): void
    {
        $this->actingAs($this->user, 'sanctum');

        StepStockBalance::factory()->count(3)->create();

        $response = $this->getJson('/api/step-stock-balances');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_create_a_step_stock_balance(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $projectItem = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $itemStepConfig = ItemStepConfigs::factory()->create();

        $data = [
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'process_step_id' => $itemStepConfig->id,
            'total_produced' => 100,
            'total_consumed' => 50,
            'available_qty' => 50,
        ];

        $response = $this->postJson('/api/step-stock-balances', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'total_produced' => 100,
                     'total_consumed' => 50,
                     'available_qty' => 50,
                 ]);

        $this->assertDatabaseHas('step_stock_balances', [
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'process_step_id' => $itemStepConfig->id,
            'total_produced' => 100,
            'total_consumed' => 50,
            'available_qty' => 50,
        ]);
    }

    /** @test */
    public function it_can_show_a_step_stock_balance(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $stepStockBalance = StepStockBalance::factory()->create();

        $response = $this->getJson("/api/step-stock-balances/{$stepStockBalance->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $stepStockBalance->id,
                 ]);
    }

    /** @test */
    public function it_can_update_a_step_stock_balance(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $stepStockBalance = StepStockBalance::factory()->create();

        $data = [
            'total_produced' => 200,
            'total_consumed' => 75,
            'available_qty' => 125,
        ];

        $response = $this->putJson("/api/step-stock-balances/{$stepStockBalance->id}", $data);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'total_produced' => 200,
                     'total_consumed' => 75,
                     'available_qty' => 125,
                 ]);

        $this->assertDatabaseHas('step_stock_balances', [
            'id' => $stepStockBalance->id,
            'total_produced' => 200,
            'total_consumed' => 75,
            'available_qty' => 125,
        ]);
    }

    /** @test */
    public function it_can_delete_a_step_stock_balance(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $stepStockBalance = StepStockBalance::factory()->create();

        $response = $this->deleteJson("/api/step-stock-balances/{$stepStockBalance->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('step_stock_balances', [
            'id' => $stepStockBalance->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $data = [
            'total_produced' => 100,
            'total_consumed' => 50,
            'available_qty' => 50,
        ];

        $response = $this->postJson('/api/step-stock-balances', $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['item_id', 'process_step_id']);
    }

    /** @test */
    public function it_validates_required_fields_when_updating(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $stepStockBalance = StepStockBalance::factory()->create();

        $data = [
            'item_id' => null,
            'process_step_id' => null,
        ];

        $response = $this->putJson("/api/step-stock-balances/{$stepStockBalance->id}", $data);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['item_id', 'process_step_id']);
    }
}
