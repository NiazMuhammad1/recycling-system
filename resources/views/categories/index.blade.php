@extends('adminlte::page')

@section('title', 'Categories')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Categories</h1>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-sm mb-0">
            <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>EWC</th>
                <th>Hazard</th>
                <th>Status</th>
                <th style="width:140px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $loop->iteration + ($categories->currentPage()-1) * $categories->perPage() }}</td>
                    <td>{{ $category->name }}</td>
                    <td><span class="badge badge-info text-capitalize">{{ str_replace('_',' ', $category->type) }}</span></td>
                    <td>{{ $category->ewc_code }}</td>
                    <td>{{ $category->hazard_codes }}</td>
                    <td>
                        @if($category->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-xs btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('categories.destroy', $category) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-xs btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">No categories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer">{{ $categories->links() }}</div>
    @endif
</div>
@stop