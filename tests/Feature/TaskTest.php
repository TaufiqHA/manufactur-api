<?php

namespace Tests\Feature;

use App\Models\Machine;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\SubAssembly;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test listing all tasks.
     */
    public function test_can_list_tasks(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]); // Ensure machine has user_id

        Task::factory()->count(3)->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'project_id',
                        'project_name',
                        'item_id',
                        'item_name',
                        'sub_assembly_id',
                        'sub_assembly_name',
                        'step',
                        'machine_id',
                        'target_qty',
                        'daily_target',
                        'completed_qty',
                        'defect_qty',
                        'status',
                        'note',
                        'total_downtime_minutes',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /**
     * Test creating a new task.
     */
    public function test_can_create_task(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        // We need to make sure these models use UUID primary keys
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();

        $data = [
            'project_id' => $project->id, // This should be a UUID if the Project model uses UUIDs
            'project_name' => $project->name,
            'item_id' => $item->id, // This should be a UUID if the ProjectItem model uses UUIDs
            'item_name' => $item->name,
            'step' => $this->faker->sentence(4),
            'target_qty' => $this->faker->numberBetween(1, 100),
            'status' => 'PENDING',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'project_name' => $data['project_name'],
            'item_name' => $data['item_name'],
            'step' => $data['step'],
            'target_qty' => $data['target_qty'],
            'status' => $data['status'],
        ]);
    }

    /**
     * Test showing a single task.
     */
    public function test_can_show_task(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $task->id,
                    'project_id' => $task->project_id,
                    'project_name' => $task->project_name,
                    'item_id' => $task->item_id,
                    'item_name' => $task->item_name,
                    'step' => $task->step,
                    'target_qty' => $task->target_qty,
                    'status' => $task->status,
                ]
            ]);
    }

    /**
     * Test updating a task.
     */
    public function test_can_update_task(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $updatedData = [
            'project_name' => $this->faker->sentence(3),
            'item_name' => $this->faker->sentence(2),
            'step' => $this->faker->sentence(4),
            'target_qty' => $this->faker->numberBetween(100, 200),
            'status' => 'IN_PROGRESS',
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'project_name' => $updatedData['project_name'],
            'item_name' => $updatedData['item_name'],
            'step' => $updatedData['step'],
            'target_qty' => $updatedData['target_qty'],
            'status' => $updatedData['status'],
        ]);
    }

    /**
     * Test deleting a task.
     */
    public function test_can_delete_task(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully'
            ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test updating task status.
     */
    public function test_can_update_task_status(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'IN_PROGRESS'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task status updated successfully',
                'data' => [
                    'status' => 'IN_PROGRESS'
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'IN_PROGRESS',
        ]);
    }

    /**
     * Test updating task completion.
     */
    public function test_can_update_task_completion(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/completion", [
            'completed_qty' => 50,
            'defect_qty' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task completion updated successfully',
                'data' => [
                    'completed_qty' => 50,
                    'defect_qty' => 5,
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'completed_qty' => 50,
            'defect_qty' => 5,
        ]);
    }

    /**
     * Test updating task downtime.
     */
    public function test_can_update_task_downtime(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/downtime", [
            'total_downtime_minutes' => 60,
            'note' => 'Machine maintenance',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task downtime updated successfully',
                'data' => [
                    'total_downtime_minutes' => 60,
                    'note' => 'Machine maintenance',
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'total_downtime_minutes' => 60,
            'note' => 'Machine maintenance',
        ]);
    }

    /**
     * Test validation for creating a task.
     */
    public function test_validation_for_creating_task(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/tasks', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
            ]);
    }

    /**
     * Test validation for updating task status.
     */
    public function test_validation_for_updating_task_status(): void
    {
        $this->actingAs($this->user, 'sanctum');

        // Create related models first to satisfy foreign key constraints
        $project = Project::factory()->create();
        $item = ProjectItem::factory()->create();
        $subAssembly = SubAssembly::factory()->create();
        $machine = Machine::factory()->create(['user_id' => $this->user->id]);

        $task = Task::factory()->create([
            'project_id' => $project->id,
            'project_name' => $project->name,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'sub_assembly_id' => $subAssembly->id,
            'sub_assembly_name' => $subAssembly->name,
            'machine_id' => $machine->id,
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'INVALID_STATUS'
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation error',
            ]);
    }
}
