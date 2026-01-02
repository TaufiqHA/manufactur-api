<?php

namespace App\Http\Controllers;

use App\Models\ReceivingGood;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ReceivingGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $receivingGoods = ReceivingGood::with(['purchaseOrder.supplier'])->latest()->paginate(10);
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            $receivingGood = ReceivingGood::create($validatedData);

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
            $receivingGood->load('purchaseOrder');
            return response()->json([
                'id' => $receivingGood->id,
                'code' => $receivingGood->code,
                'date' => $receivingGood->date->format('Y-m-d'),
                'po_id' => $receivingGood->po_id,
                'created_at' => $receivingGood->created_at,
                'updated_at' => $receivingGood->updated_at,
                'purchase_order' => $receivingGood->purchaseOrder
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
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();
            $receivingGood->update($validatedData);

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
            $receivingGood->delete();

            return response()->json([
                'success' => true,
                'message' => 'Receiving Good deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete receiving good',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
