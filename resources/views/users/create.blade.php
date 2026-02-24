@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        @include('users._form', ['user' => null])

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@stop