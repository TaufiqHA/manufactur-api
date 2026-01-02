<?php

namespace App\Http\Controllers;

use App\Models\PoItem;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PoItemController extends Controller
{
    /**
     * Display a listing of the po items.
     */
    public function index(): JsonResponse
    {
        $poItems = PoItem::with(['purchaseOrder', 'material'])->get();

        return response()->json([
            'success' => true,
            'data' => $poItems
        ]);
    }

    /**
     * Display the specified po item.
     */
    public function show(int $id): JsonResponse
    {
        $poItem = PoItem::with(['purchaseOrder', 'material'])->find($id);

        if (!$poItem) {
            return response()->json([
                'success' => false,
                'message' => 'PoItem not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $poItem
        ]);
    }

    /**
     * Store a newly created po item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'po_id' => 'required|exists:purchase_orders,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $poItem = PoItem::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $poItem->load(['purchaseOrder', 'material']),
            'message' => 'PoItem created successfully'
        ], 201);
    }

    /**
     * Update the specified po item in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $poItem = PoItem::find($id);

        if (!$poItem) {
            return response()->json([
                'success' => false,
                'message' => 'PoItem not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'po_id' => 'sometimes|required|exists:purchase_orders,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'name' => 'sometimes|required|string|max:255',
            'qty' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $poItem->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $poItem->load(['purchaseOrder', 'material']),
            'message' => 'PoItem updated successfully'
        ]);
    }

    /**
     * Remove the specified po item from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $poItem = PoItem::find($id);

        if (!$poItem) {
            return response()->json([
                'success' => false,
                'message' => 'PoItem not found'
            ], 404);
        }

        $poItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'PoItem deleted successfully'
        ]);
    }
}
