<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:projects,code',
            'name' => 'required|string|max:255',
            'customer' => 'required|string|max:255',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:PLANNED,IN_PROGRESS,COMPLETED,ON_HOLD',
            'progress' => 'required|integer|min:0|max:100',
            'qty_per_unit' => 'required|integer|min:0',
            'procurement_qty' => 'required|integer|min:0',
            'total_qty' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_locked' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $project = Project::create($validatedData);

        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResponse
    {
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:255|unique:projects,code,' . $project->id,
            'name' => 'sometimes|required|string|max:255',
            'customer' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'deadline' => 'sometimes|required|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:PLANNED,IN_PROGRESS,COMPLETED,ON_HOLD',
            'progress' => 'sometimes|required|integer|min:0|max:100',
            'qty_per_unit' => 'sometimes|required|integer|min:0',
            'procurement_qty' => 'sometimes|required|integer|min:0',
            'total_qty' => 'sometimes|required|integer|min:0',
            'unit' => 'sometimes|required|string|max:50',
            'is_locked' => 'sometimes|required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $project->update($validatedData);

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully'], 200);
    }
}
