@php
    /** @var \App\Models\User|null $user */
    $user = $user ?? null;

    // checkbox columns like your screenshot
    $actions = ['view', 'add', 'modify', 'collect', 'process'];

    // for edit mode
    $userRole = old('role', $userRole ?? optional($user?->roles->first())->name);
    $userPermissions = old('permissions', $userPermissions ?? ($user?->permissions->pluck('name')->toArray() ?? []));
@endphp

<div class="card">
    <div class="card-body">
        <div class="row">

            {{-- LEFT COLUMN --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Name *</label>
                    <div class="col-sm-8">
                        <input type="text" name="name"
                               class="form-control form-control-sm"
                               value="{{ old('name', $user?->name) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Email *</label>
                    <div class="col-sm-8">
                        <input type="email" name="email"
                               class="form-control form-control-sm"
                               value="{{ old('email', $user?->email) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Password {{ $user ? '(leave blank to keep)' : '*' }}
                    </label>
                    <div class="col-sm-8">
                        <input type="password" name="password"
                               class="form-control form-control-sm" {{ $user ? '' : 'required' }}>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Confirm Password</label>
                    <div class="col-sm-8">
                        <input type="password" name="password_confirmation"
                               class="form-control form-control-sm" {{ $user ? '' : 'required' }}>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Role *</label>
                    <div class="col-sm-8">
                        <select name="role" class="form-control form-control-sm" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" @selected($userRole === $role->name)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted"></small>
                    </div>
                </div>

            </div>

            {{-- PERMISSIONS (FULL WIDTH) --}}
            <div class="col-12 px-2 mt-2">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Permissions</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="checkAllPerms">
                                <i class="fas fa-check-double"></i> Check All
                            </button>
                            <button type="button" class="btn btn-tool" id="uncheckAllPerms">
                                <i class="fas fa-times"></i> Uncheck All
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 220px;">Module</th>
                                    @foreach($actions as $action)
                                        <th class="text-center text-capitalize">{{ $action }}</th>
                                    @endforeach
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($permissionsByModule as $module => $perms)
                                    @php
                                        $available = $perms->pluck('name')->toArray(); // e.g. collections.view
                                    @endphp

                                    <tr>
                                        <td class="font-weight-bold text-capitalize">
                                            {{ str_replace('_',' ', $module) }}
                                        </td>

                                        @foreach($actions as $action)
                                            @php
                                                $permName = "{$module}.{$action}";
                                                $exists = in_array($permName, $available, true);
                                                $checked = in_array($permName, $userPermissions, true);
                                            @endphp

                                            <td class="text-center">
                                                @if($exists)
                                                    <div class="icheck-primary d-inline">
                                                        <input type="checkbox"
                                                               id="perm_{{ md5($permName) }}"
                                                               name="permissions[]"
                                                               value="{{ $permName }}"
                                                               @checked($checked)>
                                                        <label for="perm_{{ md5($permName) }}"></label>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- row --}}
    </div>{{-- card-body --}}
</div>{{-- card --}}

@push('js')
<script>
    document.getElementById('checkAllPerms')?.addEventListener('click', function () {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
    });

    document.getElementById('uncheckAllPerms')?.addEventListener('click', function () {
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    });
</script>
@endpush