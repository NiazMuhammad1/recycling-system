<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('permission:users.view')
        //     ->only(['index', 'show']);

        // $this->middleware('permission:users.add')
        //     ->only(['create', 'store']);

        // $this->middleware('permission:users.modify')
        //     ->only(['edit', 'update']);

        // $this->middleware('permission:users.delete')
        //     ->only(['destroy']);
    }

    public function index()
    {
        $users = User::with('roles')->latest()->paginate(15);

        return view('users.index', compact('users'));
    }


    public function create()
    {
        $roles = Role::orderBy('name')->get();

        $permissionsByModule = Permission::orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        return view('users.create', compact('roles', 'permissionsByModule'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required', Password::min(8), 'confirmed'],
            'role' => ['required','string','exists:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string','exists:permissions,name'],
            'signature' => ['nullable','string'], // data url
            'signature_print_name' => ['nullable','string','max:255'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'signature' => $data['signature'] ?? null,
            'signature_print_name' => $data['signature_print_name'] ?? null,
        ]);
        

        $user->syncRoles([$data['role']]);

        // If you want per-user overrides on top of role:
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        $permissionsByModule = Permission::orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0]);

        $userRole = optional($user->roles->first())->name;

        // This includes role permissions + direct user permissions
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        return view('users.edit', compact(
            'user',
            'roles',
            'permissionsByModule',
            'userRole',
            'userPermissions'
        ));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'password' => ['nullable', Password::min(8), 'confirmed'],
            'role' => ['required','string','exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
            'signature' => ['nullable','string'], // data url
            'signature_print_name' => ['nullable','string','max:255'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->signature = $data['signature'] ?? null;
        $user->signature_print_name = $data['signature_print_name'] ?? null;
        $user->save();

        $user->syncRoles([$data['role']]);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete(); // consider SoftDeletes if you want restore later
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}