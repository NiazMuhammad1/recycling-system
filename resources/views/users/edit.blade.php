@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        @include('users._form', ['user' => $user])

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@stop