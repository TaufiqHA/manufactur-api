<?php

namespace App\Http\Controllers;

use App\Models\RfqItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RfqItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $rfqItems = RfqItem::with(['rfq', 'material'])->get();
        return response()->json($rfqItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfq_id' => 'required|exists:rfqs,id',
            'material_id' => 'required|exists:materials,id',
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rfqItem = RfqItem::create($validator->validated());

        return response()->json($rfqItem, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(RfqItem $rfqItem): JsonResponse
    {
        $rfqItem->load(['rfq', 'material']);
        return response()->json($rfqItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RfqItem $rfqItem): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfq_id' => 'sometimes|required|exists:rfqs,id',
            'material_id' => 'sometimes|required|exists:materials,id',
            'name' => 'sometimes|required|string|max:255',
            'qty' => 'sometimes|required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rfqItem->update($validator->validated());

        return response()->json($rfqItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RfqItem $rfqItem): JsonResponse
    {
        $rfqItem->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
