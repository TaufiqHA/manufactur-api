<?php

namespace App\Http\Controllers;

use App\Models\StepStockBalance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class StepStockBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $stepStockBalances = StepStockBalance::with(['projectItem', 'subAssembly', 'itemStepConfig'])->get();
        return response()->json($stepStockBalances);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:project_items,id',
            'sub_assembly_id' => 'nullable|exists:sub_assemblies,id',
            'process_step_id' => 'required|exists:item_step_configs,id',
            'total_produced' => 'required|integer|min:0',
            'total_consumed' => 'required|integer|min:0',
            'available_qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stepStockBalance = StepStockBalance::create($validator->validated());

        return response()->json($stepStockBalance->load(['projectItem', 'subAssembly', 'itemStepConfig']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(StepStockBalance $stepStockBalance): JsonResponse
    {
        $stepStockBalance->load(['projectItem', 'subAssembly', 'itemStepConfig']);
        return response()->json($stepStockBalance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StepStockBalance $stepStockBalance): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'sometimes|required|exists:project_items,id',
            'sub_assembly_id' => 'nullable|exists:sub_assemblies,id',
            'process_step_id' => 'sometimes|required|exists:item_step_configs,id',
            'total_produced' => 'sometimes|required|integer|min:0',
            'total_consumed' => 'sometimes|required|integer|min:0',
            'available_qty' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $stepStockBalance->update($validator->validated());

        return response()->json($stepStockBalance->load(['projectItem', 'subAssembly', 'itemStepConfig']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StepStockBalance $stepStockBalance): JsonResponse
    {
        $stepStockBalance->delete();

        return response()->json(null, 204);
    }
}
