@extends('adminlte::page')
@section('title','Create Collection')

@section('content_header')
<h1>Create New Collection</h1>
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger">Please fix the errors and try again.</div>
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
