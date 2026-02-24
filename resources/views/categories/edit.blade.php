@extends('adminlte::page')

@section('title', 'Edit Category')

@section('content_header')
    <h1>Edit Category</h1>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('categories.update', $category) }}">
    @csrf
    @method('PUT')

    @include('categories._form', ['category' => $category])

    <div class="mt-3">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-save"></i> Update
        </button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@stop