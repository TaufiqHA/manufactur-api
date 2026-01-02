<?php

namespace App\Http\Controllers;

use App\Models\SubAssembly;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;

class SubAssemblyController extends Controller
{
    /**
     * Display a listing of the sub assemblies.
     */
    public function index(): JsonResponse
    {
        try {
            $subAssemblies = SubAssembly::with(['item', 'material'])->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $subAssemblies
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub assemblies',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created sub assembly in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:project_items,id',
                'name' => 'required|string|max:255',
                'qty_per_parent' => 'required|integer|min:1',
                'total_needed' => 'required|integer|min:0',
                'completed_qty' => 'nullable|integer|min:0',
                'total_produced' => 'nullable|integer|min:0',
                'consumed_qty' => 'nullable|integer|min:0',
                'material_id' => 'nullable|exists:materials,id',
                'processes' => 'required|json',
                'step_stats' => 'nullable|json',
                'is_locked' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validated();

            $subAssembly = SubAssembly::create($validated);

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified sub assembly.
     */
    public function show(SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $subAssembly->load(['item', 'material']);

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified sub assembly in storage.
     */
    public function update(Request $request, SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'item_id' => 'sometimes|required|exists:project_items,id',
                'name' => 'sometimes|required|string|max:255',
                'qty_per_parent' => 'sometimes|required|integer|min:1',
                'total_needed' => 'sometimes|required|integer|min:0',
                'completed_qty' => 'sometimes|nullable|integer|min:0',
                'total_produced' => 'sometimes|nullable|integer|min:0',
                'consumed_qty' => 'sometimes|nullable|integer|min:0',
                'material_id' => 'sometimes|nullable|exists:materials,id',
                'processes' => 'sometimes|required|json',
                'step_stats' => 'sometimes|nullable|json',
                'is_locked' => 'sometimes|required|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validated = $validator->validated();

            $subAssembly->update($validated);

            return response()->json([
                'success' => true,
                'data' => $subAssembly
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified sub assembly from storage.
     */
    public function destroy(SubAssembly $subAssembly): JsonResponse
    {
        try {
            if (!$subAssembly) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sub assembly not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $subAssembly->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sub assembly deleted successfully'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sub assembly',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
