<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::with(['project', 'item', 'subAssembly', 'machine'])->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'project_name' => 'required|string|max:255',
            'item_id' => 'required|exists:project_items,id',
            'item_name' => 'required|string|max:255',
            'step' => 'required|string|max:255',
            'target_qty' => 'required|integer|min:0',
            'status' => 'required|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME',
            'sub_assembly_id' => 'nullable|exists:sub_assemblies,id',
            'sub_assembly_name' => 'nullable|string|max:255',
            'machine_id' => 'nullable|exists:machines,id',
            'daily_target' => 'nullable|integer|min:0',
            'completed_qty' => 'nullable|integer|min:0',
            'defect_qty' => 'nullable|integer|min:0',
            'note' => 'nullable|string',
            'total_downtime_minutes' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load(['project', 'item', 'subAssembly', 'machine']);

        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'sometimes|required|exists:projects,id',
            'project_name' => 'sometimes|required|string|max:255',
            'item_id' => 'sometimes|required|exists:project_items,id',
            'item_name' => 'sometimes|required|string|max:255',
            'step' => 'sometimes|required|string|max:255',
            'target_qty' => 'sometimes|required|integer|min:0',
            'status' => 'sometimes|required|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME',
            'sub_assembly_id' => 'nullable|sometimes|exists:sub_assemblies,id',
            'sub_assembly_name' => 'nullable|sometimes|string|max:255',
            'machine_id' => 'nullable|sometimes|exists:machines,id',
            'daily_target' => 'nullable|sometimes|integer|min:0',
            'completed_qty' => 'nullable|sometimes|integer|min:0',
            'defect_qty' => 'nullable|sometimes|integer|min:0',
            'note' => 'nullable|sometimes|string',
            'total_downtime_minutes' => 'nullable|sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Update the status of a task.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:PENDING,IN_PROGRESS,PAUSED,COMPLETED,DOWNTIME'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully',
            'data' => $task
        ]);
    }

    /**
     * Update the completion quantity of a task.
     */
    public function updateCompletion(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'completed_qty' => 'required|integer|min:0',
            'defect_qty' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update([
            'completed_qty' => $request->completed_qty,
            'defect_qty' => $request->defect_qty ?? $task->defect_qty
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task completion updated successfully',
            'data' => $task
        ]);
    }

    /**
     * Update the downtime for a task.
     */
    public function updateDowntime(Request $request, Task $task): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'total_downtime_minutes' => 'required|integer|min:0',
            'note' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update([
            'total_downtime_minutes' => $request->total_downtime_minutes,
            'note' => $request->note ?? $task->note
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task downtime updated successfully',
            'data' => $task
        ]);
    }
}
