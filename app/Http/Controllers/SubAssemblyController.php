<?php

namespace App\Http\Controllers;

use App\Models\SubAssembly;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class SubAssemblyController extends Controller
{
    /**
     * Display a listing of the sub assemblies.
     */
    public function index(): JsonResponse
    {
        try {
            $subAssemblies = SubAssembly::with(['item', 'material'])->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $subAssemblies
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub assemblies',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created sub assembly in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:project_items,id',
                'name' => 'required|string|max:255',
                'qty_per_parent' => 'required|integer|min:1',
                'total_needed' => 'required|integer|min:0',
                'completed_qty' => 'nullable|integer|min:0',
                'total_produced' => 'nullable|integer|min:0',
                'consumed_qty' => 'nullable|integer|min:0',
                'material_id' => 'nullable|exists:materials,id',
                'processes' => 'required|json',
                'step_stats' => 'nullable|json',
                'is_locked' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validated();

            $subAssembly = SubAssembly::create($validated);

            // Create tasks based on processes
            try {
                $this->createTasksFromProcesses($subAssembly, $validated['processes']);
            } catch (Exception $e) {
                // If task creation fails, log the error but still return success for sub assembly
                // The sub assembly was created successfully, so we don't want to fail the entire request
                \Log::error('Failed to create tasks for sub assembly: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified sub assembly.
     */
    public function show(SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $subAssembly->load(['item', 'material']);

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified sub assembly in storage.
     */
    public function update(Request $request, SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'item_id' => 'sometimes|required|exists:project_items,id',
                'name' => 'sometimes|required|string|max:255',
                'qty_per_parent' => 'sometimes|required|integer|min:1',
                'total_needed' => 'sometimes|required|integer|min:0',
                'completed_qty' => 'sometimes|nullable|integer|min:0',
                'total_produced' => 'sometimes|nullable|integer|min:0',
                'consumed_qty' => 'sometimes|nullable|integer|min:0',
                'material_id' => 'sometimes|nullable|exists:materials,id',
                'processes' => 'sometimes|required|json',
                'step_stats' => 'sometimes|nullable|json',
                'is_locked' => 'sometimes|required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validated();

            $subAssembly->update($validated);

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified sub assembly from storage.
     */
    public function destroy(SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $subAssembly->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sub assembly deleted successfully'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create tasks based on processes defined in sub assembly.
     */
    private function createTasksFromProcesses(SubAssembly $subAssembly, string $processesJson): void
    {
        $processes = json_decode($processesJson, true);

        if (!is_array($processes)) {
            return;
        }

        foreach ($processes as $process) {
            // Handle both string and object formats for processes
            $processName = null;

            if (is_string($process)) {
                // Process is a simple string
                $processName = $process;
            } elseif (is_array($process) && isset($process['name'])) {
                // Process is an object with a name property
                $processName = $process['name'];
            } else {
                continue;
            }

            if ($processName) {
                // Get the project information from the sub assembly's item
                $projectItem = $subAssembly->item;
                if (!$projectItem) {
                    continue; // Skip if project item doesn't exist
                }

                $project = $projectItem->project;
                if (!$project) {
                    continue; // Skip if project doesn't exist
                }

                // Create a task for each process
                Task::create([
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'item_id' => $projectItem->id,
                    'item_name' => $projectItem->name,
                    'sub_assembly_id' => $subAssembly->id,
                    'sub_assembly_name' => $subAssembly->name,
                    'step' => $processName,
                    'target_qty' => $subAssembly->total_needed, // Use the total needed quantity from sub assembly
                    'status' => 'PENDING', // Default status for new tasks
                    'daily_target' => null,
                    'completed_qty' => 0,
                    'defect_qty' => 0,
                    'note' => null,
                    'total_downtime_minutes' => 0,
                ]);
            }
        }

        // Create additional tasks for specific machines: LASPEN, LASMIG, PHOSPHATING, CAT, and PACKING
        $additionalTasks = ['LASPEN', 'LASMIG', 'PHOSPHATING', 'CAT', 'PACKING'];

        // Get the project information from the sub assembly's item
        $projectItem = $subAssembly->item;
        if (!$projectItem) {
            return; // Skip if project item doesn't exist
        }

        $project = $projectItem->project;
        if (!$project) {
            return; // Skip if project doesn't exist
        }

        foreach ($additionalTasks as $taskName) {
            Task::create([
                'project_id' => $project->id,
                'project_name' => $project->name,
                'item_id' => $projectItem->id,
                'item_name' => $projectItem->name,
                'sub_assembly_id' => $subAssembly->id,
                'sub_assembly_name' => $subAssembly->name,
                'step' => $taskName,
                'target_qty' => $subAssembly->total_needed, // Use the total needed quantity from sub assembly
                'status' => 'PENDING', // Default status for new tasks
                'daily_target' => null,
                'completed_qty' => 0,
                'defect_qty' => 0,
                'note' => null,
                'total_downtime_minutes' => 0,
            ]);
        }
    }
}
