@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Users</h1>

        @can('users.create')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        @endcan
    </div>
@stop

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">

            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role(s)</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>

                        <td>{{ $user->name }}</td>

                        <td>{{ $user->email }}</td>

                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge badge-info">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            @can('users.edit')
                                <a href="{{ route('users.edit', $user) }}"
                                   class="btn btn-xs btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan

                            @can('users.delete')
                                <form action="{{ route('users.destroy', $user) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-xs btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No users found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>

        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@stop