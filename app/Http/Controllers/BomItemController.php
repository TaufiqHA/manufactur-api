<?php

namespace App\Http\Controllers;

use App\Models\BomItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BomItemController extends Controller
{
    /**
     * Display a listing of the BOM items.
     */
    public function index(): JsonResponse
    {
        $bomItems = BomItem::with(['item', 'material'])->paginate(15);

        return response()->json($bomItems);
    }

    /**
     * Store a newly created BOM item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:project_items,id',
            'material_id' => 'required|exists:materials,id',
            'quantity_per_unit' => 'required|integer|min:1',
            'total_required' => 'required|integer|min:1',
            'allocated' => 'nullable|integer|min:0',
            'realized' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

        $bomItem = BomItem::create($validated);

        $bomItem->load(['item', 'material']);

        return response()->json($bomItem, Response::HTTP_CREATED);
    }

    /**
     * Display the specified BOM item.
     */
    public function show(BomItem $bomItem): JsonResponse
    {
        $bomItem->load(['item', 'material']);

        return response()->json($bomItem);
    }

    /**
     * Update the specified BOM item in storage.
     */
    public function update(Request $request, BomItem $bomItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'sometimes|required|exists:project_items,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'quantity_per_unit' => 'sometimes|required|integer|min:1',
            'total_required' => 'sometimes|required|integer|min:1',
            'allocated' => 'sometimes|nullable|integer|min:0',
            'realized' => 'sometimes|nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

        $bomItem->update($validated);

        $bomItem->load(['item', 'material']);

        return response()->json($bomItem);
    }

    /**
     * Remove the specified BOM item from storage.
     */
    public function destroy(BomItem $bomItem): JsonResponse
    {
        $bomItem->delete();

        return response()->json(null, \Illuminate\Http\Response::HTTP_NO_CONTENT);
    }
}
