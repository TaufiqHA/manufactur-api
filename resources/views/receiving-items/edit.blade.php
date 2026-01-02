@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Receiving Item</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('receiving-items.update', $receivingItem) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="receiving_id">Receiving</label>
                            <select name="receiving_id" id="receiving_id" class="form-control @error('receiving_id') is-invalid @enderror" required>
                                <option value="">Select Receiving</option>
                                @foreach($receivingGoods as $receiving)
                                    <option value="{{ $receiving->id }}" {{ old('receiving_id', $receivingItem->receiving_id) == $receiving->id ? 'selected' : '' }}>
                                        {{ $receiving->code }} ({{ $receiving->date }})
                                    </option>
                                @endforeach
                            </select>
                            @error('receiving_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="material_id">Material</label>
                            <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $material)
                                    <option value="{{ $material->id }}" {{ old('material_id', $receivingItem->material_id) == $material->id ? 'selected' : '' }}>
                                        {{ $material->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('material_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $receivingItem->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="qty">Quantity</label>
                            <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror" 
                                   value="{{ old('qty', $receivingItem->qty) }}" min="1" required>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('receiving-items.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection