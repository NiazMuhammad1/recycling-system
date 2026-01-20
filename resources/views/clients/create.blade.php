@extends('adminlte::page')

@section('title', 'Create Client')

@section('content_header')
    <h1>Create Client</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf

        @include('clients._form')

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@stop
