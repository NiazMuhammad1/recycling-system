@extends('adminlte::page')

@section('title', 'Create Category')

@section('content_header')
    <h1>Create Category</h1>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('categories.store') }}">
    @csrf

    @include('categories._form', ['category' => null])

    <div class="mt-3">
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-save"></i> Save
        </button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@stop