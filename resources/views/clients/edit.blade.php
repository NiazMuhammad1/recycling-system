@extends('adminlte::page')

@section('title', 'Edit Client')

@section('content_header')
    <h1>Edit Client</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf
        @method('PUT')

        @include('clients._form', ['client' => $client])

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@stop
