<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $materials = Material::all();
        return response()->json($materials);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:materials,code',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|integer|min:0',
            'safety_stock' => 'required|integer|min:0',
            'price_per_unit' => 'required|numeric|min:0',
            'category' => 'required|in:RAW,FINISHING,HARDWARE',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $material = Material::create($validator->validated());

        return response()->json($material, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material): JsonResponse
    {
        return response()->json($material);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|unique:materials,code,' . $material->id,
            'name' => 'sometimes|string|max:255',
            'unit' => 'sometimes|string|max:50',
            'current_stock' => 'sometimes|integer|min:0',
            'safety_stock' => 'sometimes|integer|min:0',
            'price_per_unit' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|in:RAW,FINISHING,HARDWARE',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $material->update($validator->validated());

        return response()->json($material);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material): JsonResponse
    {
        $material->delete();

        return response()->json(null, 204);
    }
}
