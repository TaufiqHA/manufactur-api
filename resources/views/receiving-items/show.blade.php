@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Receiving Item Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID:</label>
                                <p>{{ $receivingItem->id }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label>Receiving:</label>
                                <p>{{ $receivingItem->receiving->code ?? 'N/A' }} ({{ $receivingItem->receiving->date ?? 'N/A' }})</p>
                            </div>
                            
                            <div class="form-group">
                                <label>Material:</label>
                                <p>{{ $receivingItem->material->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name:</label>
                                <p>{{ $receivingItem->name }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label>Quantity:</label>
                                <p>{{ $receivingItem->qty }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label>Created At:</label>
                                <p>{{ $receivingItem->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                            
                            <div class="form-group">
                                <label>Updated At:</label>
                                <p>{{ $receivingItem->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <a href="{{ route('receiving-items.index') }}" class="btn btn-secondary">Back to List</a>
                        <a href="{{ route('receiving-items.edit', $receivingItem) }}" class="btn btn-warning">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection