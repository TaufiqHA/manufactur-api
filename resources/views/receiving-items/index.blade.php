@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Receiving Items</h3>
                    <div class="card-tools">
                        <a href="{{ route('receiving-items.create') }}" class="btn btn-primary">Add New</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Receiving</th>
                                    <th>Material</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receivingItems as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->receiving->code ?? 'N/A' }}</td>
                                        <td>{{ $item->material->name ?? 'N/A' }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('receiving-items.show', $item) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('receiving-items.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('receiving-items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No receiving items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $receivingItems->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection