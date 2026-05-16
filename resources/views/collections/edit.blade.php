@extends('adminlte::page')
@section('title','Edit Collection')
@section('plugins.Select2', true)
@section('content_header')
<h1>Edit Collection {{ $collection->collection_code }}</h1>
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

<form method="POST" action="{{ route('collections.update', $collection) }}">
    @csrf
    @method('PUT')

    @include('collections._form', ['collection' => $collection])

    <div class="mt-3">
        <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update</button>
        <a href="{{ route('collections.show', $collection) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@stop
