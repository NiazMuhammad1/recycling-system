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

            <div class="col-md-6 pr-lg-5">

                <div class="card mt-3">
                    <div class="card-header">
                        <strong>Driver Signature</strong>
                        <small class="text-muted">(draw with mouse/touch)</small>
                    </div>

                    <div class="card-body">
                        <input type="hidden" name="signature" id="signature_png" value="{{ old('signature', $user?->signature) }}">

                        <div class="border rounded p-2" style="background:#fff;">
                            <canvas id="sigCanvas" height="160" style="width:100%;"></canvas>
                        </div>

                        <div class="mt-2 d-flex">
                            <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="sigClear">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="sigSaveToHidden">
                                <i class="fas fa-save"></i> Use this signature
                            </button>
                        </div>

                        

                        <small class="text-muted d-block mt-2">
                            Tip: click “Use this signature” after signing.
                        </small>
                    </div>
                </div>

            </div>
            <div class="col-md-6 pr-lg-5">

                <div class="mt-3">
                    <label>Signature Print Name</label>
                    <input class="form-control form-control-sm"
                        name="signature_print_name"
                        value="{{ old('signature_print_name', $user?->signature_print_name) }}">
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
    (function () {
    const canvas = document.getElementById('sigCanvas');
    if (!canvas) return;

    // make canvas match container width (retina safe)
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = 160 * ratio;
        const ctx = canvas.getContext('2d');
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        // redraw existing
        const existing = document.getElementById('signature_png')?.value;
        if (existing) {
            const img = new Image();
            img.onload = () => ctx.drawImage(img, 0, 0, rect.width, 160);
            img.src = existing;
        }
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    const ctx = canvas.getContext('2d');
    let drawing = false;
    let last = {x:0,y:0};

    function pos(e) {
        const r = canvas.getBoundingClientRect();
        const x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
        const y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
        return {x,y};
    }

    function start(e) {
        drawing = true;
        last = pos(e);
        e.preventDefault();
    }

    function move(e) {
        if (!drawing) return;
        const p = pos(e);
        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        last = p;
        e.preventDefault();
    }

    function stop(e) {
        drawing = false;
        e.preventDefault();
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', stop);

    canvas.addEventListener('touchstart', start, {passive:false});
    canvas.addEventListener('touchmove', move, {passive:false});
    window.addEventListener('touchend', stop, {passive:false});

    document.getElementById('sigClear')?.addEventListener('click', () => {
        const r = canvas.getBoundingClientRect();
        ctx.clearRect(0, 0, r.width, 160);
        document.getElementById('signature_png').value = '';
    });

    document.getElementById('sigSaveToHidden')?.addEventListener('click', () => {
        document.getElementById('signature_png').value = canvas.toDataURL('image/png');
    });
})();
</script>
@endpush