<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MachineController extends Controller
{
    /**
     * Display a listing of the machines.
     */
    public function index(): JsonResponse
    {
        $machines = Machine::with('user')->get();
        return response()->json($machines);
    }

    /**
     * Store a newly created machine in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = $this->validateMachine($request);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $machine = Machine::create($validatedData);

        return response()->json($machine, 201);
    }

    /**
     * Display the specified machine.
     */
    public function show(Machine $machine): JsonResponse
    {
        // Ensure the user can only access their own machines
        if ($machine->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($machine->load('user'));
    }

    /**
     * Update the specified machine in storage.
     */
    public function update(Request $request, Machine $machine): JsonResponse
    {
        // Ensure the user can only update their own machines
        if ($machine->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = $this->validateMachine($request, $machine->id);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $machine->update($validatedData);

        return response()->json($machine);
    }

    /**
     * Remove the specified machine from storage.
     */
    public function destroy(Machine $machine): JsonResponse
    {
        // Ensure the user can only delete their own machines
        if ($machine->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $machine->delete();

        return response()->json(null, 204);
    }

    /**
     * Validate machine data.
     */
    private function validateMachine(Request $request, $machineId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'capacity_per_hour' => 'required|integer|min:0',
            'status' => 'required|in:IDLE,RUNNING,MAINTENANCE,OFFLINE,DOWNTIME',
            'is_maintenance' => 'required|boolean',
        ];

        // If updating, make the validation rules conditional
        if ($machineId) {
            $rules['user_id'] = 'sometimes|exists:users,id';
            $rules['code'] = 'sometimes|string|max:255|unique:machines,code,' . $machineId;
            $rules['name'] = 'sometimes|string|max:255';
            $rules['type'] = 'sometimes|string|max:255';
            $rules['capacity_per_hour'] = 'sometimes|integer|min:0';
            $rules['status'] = 'sometimes|in:IDLE,RUNNING,MAINTENANCE,OFFLINE,DOWNTIME';
            $rules['is_maintenance'] = 'sometimes|boolean';
        } else {
            // For create, ensure the code is unique
            $rules['code'] .= '|unique:machines';
        }

        return \Validator::make($request->all(), $rules);
    }
}
