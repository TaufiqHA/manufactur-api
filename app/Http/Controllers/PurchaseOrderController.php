<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Rfq;
use App\Models\PoItem;
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
        $purchaseOrders = PurchaseOrder::with(['supplier', 'rfq', 'poItems'])->paginate(10);

        // Transform the collection to rename poItems to items
        $purchaseOrders->getCollection()->transform(function ($purchaseOrder) {
            $purchaseOrder->items = $purchaseOrder->poItems;
            unset($purchaseOrder->poItems);
            return $purchaseOrder;
        });

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
            'po_items' => 'required|array',
            'po_items.*.material_id' => 'required|exists:materials,id',
            'po_items.*.name' => 'required|string|max:255',
            'po_items.*.qty' => 'required|integer|min:1',
            'po_items.*.price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $poItemsData = $validatedData['po_items'];
        unset($validatedData['po_items']);

        $purchaseOrder = PurchaseOrder::create($validatedData);

        // Create PoItems for the Purchase Order
        foreach ($poItemsData as $poItemData) {
            $poItemData['po_id'] = $purchaseOrder->id;
            PoItem::create($poItemData);
        }

        // Load the relationship to return the created PoItems
        $purchaseOrder->load(['supplier', 'rfq', 'poItems']);

        // Rename poItems to items in the response
        $purchaseOrder->items = $purchaseOrder->poItems;
        unset($purchaseOrder->poItems);

        return response()->json($purchaseOrder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder): JsonResponse
    {
        $purchaseOrder->load(['supplier', 'rfq', 'poItems']);

        // Rename poItems to items in the response
        $purchaseOrder->items = $purchaseOrder->poItems;
        unset($purchaseOrder->poItems);

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

        // Load relationships including items for consistent response
        $purchaseOrder->load(['supplier', 'rfq', 'poItems']);

        // Rename poItems to items in the response
        $purchaseOrder->items = $purchaseOrder->poItems;
        unset($purchaseOrder->poItems);

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
