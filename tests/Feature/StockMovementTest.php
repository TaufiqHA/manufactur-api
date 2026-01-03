<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\ItemStepConfigs;
use App\Models\Task;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_all_stock_movements(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create some stock movements with proper relationships
        for ($i = 0; $i < 3; $i++) {
            // Create related models with proper relationships first
            $project = \App\Models\Project::factory()->create();
            $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
            $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
            $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
            $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
            $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);

            // Create task with proper relationships
            $task = Task::factory()->create([
                'project_id' => $project->id,
                'project_name' => $project->name,
                'item_id' => $projectItem->id,
                'item_name' => $projectItem->name,
                'sub_assembly_id' => $subAssembly->id,
                'sub_assembly_name' => $subAssembly->name,
                'machine_id' => $machine->id,
            ]);

            StockMovement::factory()->create([
                'item_id' => $projectItem->id,
                'sub_assembly_id' => $subAssembly->id,
                'source_step_id' => $sourceStep->id,
                'target_step_id' => $targetStep->id,
                'task_id' => $task->id,
                'created_by' => $this->user->id,
            ]);
        }

        // Make the request
        $response = $this->getJson('/api/stock-movements');

        // Assert the response
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data',
                     'pagination' => [
                         'current_page',
                         'per_page',
                         'total',
                         'last_page'
                     ]
                 ]);

        $this->assertCount(3, $response['data']);
    }

    public function test_can_get_single_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create related models with proper relationships first
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);

        // Create task with proper relationships
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $projectItem->id,
            'item_name' => $projectItem->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $stockMovement = StockMovement::factory()->create([
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
        ]);

        // Make the request
        $response = $this->getJson("/api/stock-movements/{$stockMovement->id}");

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $stockMovement->id,
                         'quantity' => $stockMovement->quantity,
                         'good_qty' => $stockMovement->good_qty,
                         'defect_qty' => $stockMovement->defect_qty,
                         'movement_type' => $stockMovement->movement_type,
                     ]
                 ]);
    }

    public function test_can_create_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create related models with proper relationships
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'machine_id' => $machine->id
        ]);

        // Prepare data for the request
        $data = [
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
            'quantity' => 50,
            'good_qty' => 45,
            'defect_qty' => 5,
            'movement_type' => 'PRODUCTION',
            'shift' => 'SHIFT_1',
        ];

        // Make the request
        $response = $this->postJson('/api/stock-movements', $data);

        // Assert the response
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Stock movement created successfully',
                 ])
                 ->assertJsonPath('data.item_id', $data['item_id'])
                 ->assertJsonPath('data.sub_assembly_id', $data['sub_assembly_id'])
                 ->assertJsonPath('data.source_step_id', $data['source_step_id'])
                 ->assertJsonPath('data.target_step_id', $data['target_step_id'])
                 ->assertJsonPath('data.task_id', $data['task_id'])
                 ->assertJsonPath('data.created_by', $data['created_by'])
                 ->assertJsonPath('data.quantity', $data['quantity'])
                 ->assertJsonPath('data.good_qty', $data['good_qty'])
                 ->assertJsonPath('data.defect_qty', $data['defect_qty'])
                 ->assertJsonPath('data.movement_type', $data['movement_type'])
                 ->assertJsonPath('data.shift', $data['shift']);

        // Assert the record was created in the database
        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $data['item_id'],
            'sub_assembly_id' => $data['sub_assembly_id'],
            'source_step_id' => $data['source_step_id'],
            'target_step_id' => $data['target_step_id'],
            'task_id' => $data['task_id'],
            'created_by' => $data['created_by'],
            'quantity' => $data['quantity'],
            'good_qty' => $data['good_qty'],
            'defect_qty' => $data['defect_qty'],
            'movement_type' => $data['movement_type'],
            'shift' => $data['shift'],
        ]);
    }

    public function test_can_update_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a stock movement with proper relationships
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $projectItem->id,
            'item_name' => $projectItem->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $stockMovement = StockMovement::factory()->create([
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
        ]);

        // Create related models for the update
        $updateProject = \App\Models\Project::factory()->create();
        $updateProjectItem = ProjectItem::factory()->create(['project_id' => $updateProject->id]);
        $updateSubAssembly = SubAssembly::factory()->create(['item_id' => $updateProjectItem->id]);
        $updateMachine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $updateSourceStep = ItemStepConfigs::factory()->create(['item_id' => $updateProjectItem->id]);
        $updateTargetStep = ItemStepConfigs::factory()->create(['item_id' => $updateProjectItem->id]);
        $updateTask = Task::factory()->create([
            'project_id' => $updateProject->id,
            'project_name' => $updateProject->name,
            'item_id' => $updateProjectItem->id,
            'item_name' => $updateProjectItem->name,
            'sub_assembly_id' => $updateSubAssembly->id,
            'sub_assembly_name' => $updateSubAssembly->name,
            'machine_id' => $updateMachine->id,
        ]);

        // Prepare update data
        $data = [
            'item_id' => $updateProjectItem->id,
            'sub_assembly_id' => $updateSubAssembly->id,
            'source_step_id' => $updateSourceStep->id,
            'target_step_id' => $updateTargetStep->id,
            'task_id' => $updateTask->id,
            'created_by' => $this->user->id,
            'quantity' => 75,
            'good_qty' => 70,
            'defect_qty' => 5,
            'movement_type' => 'CONSUMPTION',
            'shift' => 'SHIFT_2',
        ];

        // Make the request
        $response = $this->putJson("/api/stock-movements/{$stockMovement->id}", $data);

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Stock movement updated successfully',
                     'data' => [
                         'id' => $stockMovement->id,
                         'item_id' => $data['item_id'],
                         'sub_assembly_id' => $data['sub_assembly_id'],
                         'source_step_id' => $data['source_step_id'],
                         'target_step_id' => $data['target_step_id'],
                         'task_id' => $data['task_id'],
                         'created_by' => $data['created_by'],
                         'quantity' => $data['quantity'],
                         'good_qty' => $data['good_qty'],
                         'defect_qty' => $data['defect_qty'],
                         'movement_type' => $data['movement_type'],
                         'shift' => $data['shift'],
                     ]
                 ]);

        // Assert the record was updated in the database
        $this->assertDatabaseHas('stock_movements', [
            'id' => $stockMovement->id,
            'item_id' => $data['item_id'],
            'sub_assembly_id' => $data['sub_assembly_id'],
            'source_step_id' => $data['source_step_id'],
            'target_step_id' => $data['target_step_id'],
            'task_id' => $data['task_id'],
            'created_by' => $data['created_by'],
            'quantity' => $data['quantity'],
            'good_qty' => $data['good_qty'],
            'defect_qty' => $data['defect_qty'],
            'movement_type' => $data['movement_type'],
            'shift' => $data['shift'],
        ]);
    }

    public function test_can_delete_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a stock movement with proper relationships
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'machine_id' => $machine->id
        ]);

        $stockMovement = StockMovement::factory()->create([
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
        ]);

        // Make the request
        $response = $this->deleteJson("/api/stock-movements/{$stockMovement->id}");

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Stock movement deleted successfully'
                 ]);

        // Assert the record was deleted from the database
        $this->assertDatabaseMissing('stock_movements', [
            'id' => $stockMovement->id
        ]);
    }

    public function test_validation_for_creating_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create related models first
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'machine_id' => $machine->id
        ]);

        // Prepare invalid data (missing required fields)
        $data = [
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
            'quantity' => -5, // Invalid: negative quantity
            'good_qty' => 10,
            'defect_qty' => 5,
            'movement_type' => 'INVALID_TYPE', // Invalid movement type
        ];

        // Make the request
        $response = $this->postJson('/api/stock-movements', $data);

        // Assert the response has validation errors
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors'
                 ]);
    }

    public function test_validation_for_updating_stock_movement(): void
    {
        // Authenticate the user
        $this->actingAs($this->user, 'sanctum');

        // Create a stock movement with proper relationships
        $project = \App\Models\Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(['project_id' => $project->id]);
        $subAssembly = SubAssembly::factory()->create(['item_id' => $projectItem->id]);
        $machine = \App\Models\Machine::factory()->create(['user_id' => $this->user->id]);
        $sourceStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $targetStep = ItemStepConfigs::factory()->create(['item_id' => $projectItem->id]);
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'machine_id' => $machine->id
        ]);

        $stockMovement = StockMovement::factory()->create([
            'item_id' => $projectItem->id,
            'sub_assembly_id' => $subAssembly->id,
            'source_step_id' => $sourceStep->id,
            'target_step_id' => $targetStep->id,
            'task_id' => $task->id,
            'created_by' => $this->user->id,
        ]);

        // Prepare invalid update data
        $data = [
            'quantity' => -10, // Invalid: negative quantity
            'movement_type' => 'INVALID_TYPE', // Invalid movement type
        ];

        // Make the request
        $response = $this->putJson("/api/stock-movements/{$stockMovement->id}", $data);

        // Assert the response has validation errors
        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors'
                 ]);
    }

    public function test_unauthorized_access_to_stock_movements(): void
    {
        // Make a request without authentication
        $response = $this->getJson('/api/stock-movements');

        // Assert unauthorized status
        $response->assertStatus(401);
    }
}
