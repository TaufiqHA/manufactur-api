<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the stock movements.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StockMovement::with(['item', 'subAssembly', 'sourceStep', 'targetStep', 'task']);

        // Add filtering capabilities
        if ($request->has('item_id')) {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('sub_assembly_id')) {
            $query->where('sub_assembly_id', $request->sub_assembly_id);
        }

        if ($request->has('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        $perPage = $request->get('per_page', 15);

        $stockMovements = $query->paginate($perPage);

        return response()->json([
            'data' => $stockMovements->items(),
            'pagination' => [
                'current_page' => $stockMovements->currentPage(),
                'per_page' => $stockMovements->perPage(),
                'total' => $stockMovements->total(),
                'last_page' => $stockMovements->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created stock movement in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:project_items,id',
            'sub_assembly_id' => 'nullable|exists:sub_assemblies,id',
            'source_step_id' => 'nullable|exists:item_step_configs,id',
            'target_step_id' => 'required|exists:item_step_configs,id',
            'task_id' => 'nullable|exists:tasks,id',
            'created_by' => 'nullable|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'good_qty' => 'required|integer|min:0',
            'defect_qty' => 'required|integer|min:0',
            'movement_type' => ['required', Rule::in(['PRODUCTION', 'CONSUMPTION', 'ADJUSTMENT'])],
            'shift' => ['nullable', Rule::in(['SHIFT_1', 'SHIFT_2', 'SHIFT_3'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate that good_qty + defect_qty equals quantity
        if ($request->good_qty + $request->defect_qty != $request->quantity) {
            return response()->json([
                'error' => 'The sum of good_qty and defect_qty must equal the quantity.'
            ], 422);
        }

        $stockMovement = StockMovement::create($validator->validated());

        return response()->json([
            'message' => 'Stock movement created successfully',
            'data' => $stockMovement->load(['item', 'subAssembly', 'sourceStep', 'targetStep', 'task'])
        ], 201);
    }

    /**
     * Display the specified stock movement.
     */
    public function show(StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->load(['item', 'subAssembly', 'sourceStep', 'targetStep', 'task']);

        return response()->json([
            'data' => $stockMovement
        ]);
    }

    /**
     * Update the specified stock movement in storage.
     */
    public function update(Request $request, StockMovement $stockMovement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'sometimes|required|exists:project_items,id',
            'sub_assembly_id' => 'nullable|exists:sub_assemblies,id',
            'source_step_id' => 'nullable|exists:item_step_configs,id',
            'target_step_id' => 'sometimes|required|exists:item_step_configs,id',
            'task_id' => 'nullable|exists:tasks,id',
            'created_by' => 'nullable|exists:users,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'good_qty' => 'sometimes|required|integer|min:0',
            'defect_qty' => 'sometimes|required|integer|min:0',
            'movement_type' => ['sometimes', 'required', Rule::in(['PRODUCTION', 'CONSUMPTION', 'ADJUSTMENT'])],
            'shift' => ['nullable', Rule::in(['SHIFT_1', 'SHIFT_2', 'SHIFT_3'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate that good_qty + defect_qty equals quantity if both are provided
        $quantity = $request->has('quantity') ? $request->quantity : $stockMovement->quantity;
        $goodQty = $request->has('good_qty') ? $request->good_qty : $stockMovement->good_qty;
        $defectQty = $request->has('defect_qty') ? $request->defect_qty : $stockMovement->defect_qty;

        if ($goodQty + $defectQty != $quantity) {
            return response()->json([
                'error' => 'The sum of good_qty and defect_qty must equal the quantity.'
            ], 422);
        }

        $stockMovement->update($validator->validated());

        return response()->json([
            'message' => 'Stock movement updated successfully',
            'data' => $stockMovement->load(['item', 'subAssembly', 'sourceStep', 'targetStep', 'task'])
        ]);
    }

    /**
     * Remove the specified stock movement from storage.
     */
    public function destroy(StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->delete();

        return response()->json([
            'message' => 'Stock movement deleted successfully'
        ], 200);
    }
}
