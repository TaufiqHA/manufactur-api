<?php

namespace App\Http\Controllers;

use App\Models\ProjectItem;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProjectItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $projectItems = ProjectItem::with('project')->paginate(10);

        return response()->json($projectItems);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
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
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'qty_set' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_bom_locked' => 'required|boolean',
            'is_workflow_locked' => 'required|boolean',
            'flow_type' => 'required|in:OLD,NEW',
            'warehouse_qty' => 'required|integer|min:0',
            'shipped_qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $projectItem = ProjectItem::create($request->all());

        return response()->json([
            'message' => 'Project item created successfully.',
            'data' => $projectItem
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectItem $projectItem): JsonResponse
    {
        return response()->json($projectItem->load('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectItem $projectItem): JsonResponse
    {
        $projects = Project::all();

        return response()->json([
            'project_item' => $projectItem,
            'projects' => $projects
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectItem $projectItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'qty_set' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'is_bom_locked' => 'required|boolean',
            'is_workflow_locked' => 'required|boolean',
            'flow_type' => 'required|in:OLD,NEW',
            'warehouse_qty' => 'required|integer|min:0',
            'shipped_qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $projectItem->update($request->all());

        return response()->json([
            'message' => 'Project item updated successfully.',
            'data' => $projectItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectItem $projectItem): JsonResponse
    {
        try {
            $projectItem->delete();

            return response()->json([
                'message' => 'Project item deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete project item.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
