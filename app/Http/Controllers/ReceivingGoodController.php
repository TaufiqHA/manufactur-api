<?php

namespace App\Http\Controllers;

use App\Models\ReceivingGood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReceivingGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $receivingGoods = ReceivingGood::with(['purchaseOrder.supplier', 'items'])->latest()->paginate(10);
            return response()->json($receivingGoods);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve receiving goods',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:255',
                'date' => 'required|date',
                'po_id' => 'required|exists:purchase_orders,id',
                'items' => 'required|array',
                'items.*.material_id' => 'required|exists:materials,id',
                'items.*.name' => 'required|string|max:255',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            // Extract items data before creating the receiving good
            $itemsData = $validatedData['items'];
            unset($validatedData['items']);

            \DB::beginTransaction();

            $receivingGood = ReceivingGood::create($validatedData);

            // Create receiving items and update material stock
            foreach ($itemsData as $itemData) {
                $itemData['receiving_id'] = $receivingGood->id;
                \App\Models\ReceivingItem::create($itemData);

                // Update material stock
                $material = \App\Models\Material::find($itemData['material_id']);
                if ($material) {
                    $material->current_stock += $itemData['qty'];
                    $material->save();
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receiving Good created successfully.',
                'data' => [
                    'id' => $receivingGood->id,
                    'code' => $receivingGood->code,
                    'date' => $receivingGood->date->format('Y-m-d'),
                    'po_id' => $receivingGood->po_id,
                    'created_at' => $receivingGood->created_at,
                    'updated_at' => $receivingGood->updated_at,
                ]
            ], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create receiving good',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceivingGood $receivingGood): JsonResponse
    {
        try {
            $receivingGood->load(['purchaseOrder', 'items.material']);
            return response()->json([
                'id' => $receivingGood->id,
                'code' => $receivingGood->code,
                'date' => $receivingGood->date->format('Y-m-d'),
                'po_id' => $receivingGood->po_id,
                'created_at' => $receivingGood->created_at,
                'updated_at' => $receivingGood->updated_at,
                'purchase_order' => $receivingGood->purchaseOrder,
                'items' => $receivingGood->items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve receiving good',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReceivingGood $receivingGood): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:255',
                'date' => 'required|date',
                'po_id' => 'required|exists:purchase_orders,id',
                'items' => 'sometimes|array',
                'items.*.material_id' => 'required|exists:materials,id',
                'items.*.name' => 'required|string|max:255',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            // Extract items data if present
            $itemsData = null;
            if (isset($validatedData['items'])) {
                $itemsData = $validatedData['items'];
                unset($validatedData['items']);
            }

            \DB::beginTransaction();

            $receivingGood->update($validatedData);

            // Update receiving items if provided
            if ($itemsData !== null) {
                // Get existing items to revert stock changes
                $existingItems = $receivingGood->items;
                foreach ($existingItems as $existingItem) {
                    $material = \App\Models\Material::find($existingItem->material_id);
                    if ($material) {
                        $material->current_stock -= $existingItem->qty;
                        $material->save();
                    }
                }

                // Delete existing items
                $receivingGood->items()->delete();

                // Create new items and update material stock
                foreach ($itemsData as $itemData) {
                    $itemData['receiving_id'] = $receivingGood->id;
                    \App\Models\ReceivingItem::create($itemData);

                    // Update material stock
                    $material = \App\Models\Material::find($itemData['material_id']);
                    if ($material) {
                        $material->current_stock += $itemData['qty'];
                        $material->save();
                    }
                }
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receiving Good updated successfully.',
                'data' => [
                    'id' => $receivingGood->id,
                    'code' => $receivingGood->code,
                    'date' => $receivingGood->date->format('Y-m-d'),
                    'po_id' => $receivingGood->po_id,
                    'created_at' => $receivingGood->created_at,
                    'updated_at' => $receivingGood->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update receiving good',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceivingGood $receivingGood): JsonResponse
    {
        try {
            \DB::beginTransaction();

            // Get existing items to revert stock changes
            $existingItems = $receivingGood->items;
            foreach ($existingItems as $existingItem) {
                $material = \App\Models\Material::find($existingItem->material_id);
                if ($material) {
                    $material->current_stock -= $existingItem->qty;
                    $material->save();
                }
            }

            $receivingGood->delete();

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receiving Good deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete receiving good',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
