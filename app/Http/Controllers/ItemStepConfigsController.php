<?php

namespace App\Http\Controllers;

use App\Models\ItemStepConfigs;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ItemStepConfigsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $itemStepConfigs = ItemStepConfigs::with('item')->get();

        return response()->json([
            'success' => true,
            'data' => $itemStepConfigs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:project_items,id',
            'step' => 'required|string|max:255',
            'sequence' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $itemStepConfig = ItemStepConfigs::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item step config created successfully',
            'data' => $itemStepConfig->load('item')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemStepConfigs $itemStepConfig): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $itemStepConfig->load('item')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemStepConfigs $itemStepConfig): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'sometimes|required|exists:project_items,id',
            'step' => 'sometimes|required|string|max:255',
            'sequence' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $itemStepConfig->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Item step config updated successfully',
            'data' => $itemStepConfig->load('item')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemStepConfigs $itemStepConfig): JsonResponse
    {
        $itemStepConfig->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item step config deleted successfully'
        ]);
    }

    /**
     * Get all step configs for a specific project item.
     */
    public function getByItemId(int $itemId): JsonResponse
    {
        $item = ProjectItem::find($itemId);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Project item not found'
            ], 404);
        }

        $itemStepConfigs = ItemStepConfigs::where('item_id', $itemId)
            ->orderBy('sequence')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $itemStepConfigs
        ]);
    }
}
