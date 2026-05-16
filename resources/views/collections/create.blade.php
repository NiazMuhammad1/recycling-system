@extends('adminlte::page')
@section('title','Create Collection')
@section('plugins.Select2', true)
@section('content_header')
<h1>Create New Collection</h1>
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('collections.store') }}">
    @csrf

    @include('collections._form')

    <div class="mt-3">
        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
        <a href="{{ route('collections.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@stop
