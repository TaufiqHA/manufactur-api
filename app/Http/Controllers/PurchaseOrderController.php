<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'rfq'])->paginate(10);

        return response()->json($purchaseOrders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:purchase_orders,code',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'rfq_id' => 'required|exists:rfqs,id',
            'description' => 'nullable|string',
            'status' => 'required|in:OPEN,RECEIVED',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $purchaseOrder = PurchaseOrder::create($validator->validated());

        return response()->json($purchaseOrder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->load(['supplier', 'rfq']);

        return response()->json($purchaseOrder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:purchase_orders,code,' . $purchaseOrder->id,
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'rfq_id' => 'required|exists:rfqs,id',
            'description' => 'nullable|string',
            'status' => 'required|in:OPEN,RECEIVED',
            'grand_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $purchaseOrder->update($validator->validated());

        return response()->json($purchaseOrder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->delete();

        return response()->json(['message' => 'Purchase order deleted successfully']);
    }
}
