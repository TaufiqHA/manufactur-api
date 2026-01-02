<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use App\Models\ReceivingGood;
use App\Models\ReceivingItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReceivingItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $receivingItems = ReceivingItem::with(['receiving', 'material'])->paginate(10);

            return response()->json($receivingItems);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve receiving items.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiving_id' => 'required|exists:receiving_goods,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $receivingItem = ReceivingItem::create($validated);

            return response()->json([
                'message' => 'Receiving item created successfully.',
                'data' => $receivingItem->load(['receiving', 'material'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create receiving item.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivingItem $receivingItem): JsonResponse
    {
        $receivingItem->load(['receiving', 'material']);

        return response()->json($receivingItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceivingItem $receivingItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiving_id' => 'required|exists:receiving_goods,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $receivingItem->update($validated);

            return response()->json([
                'message' => 'Receiving item updated successfully.',
                'data' => $receivingItem->load(['receiving', 'material'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update receiving item.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivingItem $receivingItem): JsonResponse
    {
        try {
            $receivingItem->delete();

            return response()->json([
                'message' => 'Receiving item deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete receiving item.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
