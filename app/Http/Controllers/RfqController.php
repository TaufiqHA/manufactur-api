<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RfqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $rfqs = Rfq::all();
        return response()->json($rfqs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:DRAFT,PO_CREATED',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rfq = Rfq::create($validator->validated());

        return response()->json($rfq, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rfq $rfq): JsonResponse
    {
        return response()->json($rfq);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rfq $rfq): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:DRAFT,PO_CREATED',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rfq->update($validator->validated());

        return response()->json($rfq);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rfq $rfq): JsonResponse
    {
        $rfq->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
