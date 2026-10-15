<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function roles(Request $request)
    {
        return Role::query()->where('guard_name', 'web')
            ->when(!$request->user()->hasRole('super-user'), fn ($query) => $query->where('name', '!=', 'super-user'))
            ->get();
    }

    private function authorizeTarget(Request $request, User $user): void
    {
        abort_if($user->hasRole('super-user') && !$request->user()->hasRole('super-user'), 403);
    }

    private function isLastOwner(User $user): bool
    {
        return $user->hasRole('super-user') && User::role('super-user')->count() <= 1;
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users')->ignore($user?->id)],
            'password' => [$user ? 'sometimes' : 'required', 'string', 'min:12', 'max:72'],
            'role' => ['required', 'string', Rule::in($this->roles($request)->pluck('name')->all())],
            'website_name' => ['nullable', 'string', 'max:255'],
            'website_login' => ['nullable', 'string', 'max:255'],
            'website_password' => ['nullable', 'string', 'max:1000'],
            'services' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    public function index()
    {
        return Inertia::render('Dashboard/Users/Index', ['users' => User::with('roles')->latest()->get()]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Dashboard/Users/Create', ['roles' => $this->roles($request)]);
    }

    public function store(Request $request)
    {
        $data = $this->validateUser($request);
        $role = $data['role'];
        unset($data['role']);
        $user = User::create($data);
        $user->assignRole($role);

        return redirect('/dashboard/users');
    }

    public function edit(Request $request, User $user)
    {
        $this->authorizeTarget($request, $user);

        return Inertia::render('Dashboard/Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => $this->roles($request),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeTarget($request, $user);
        $data = $this->validateUser($request, $user);
        $role = $data['role'];
        abort_if($role !== 'super-user' && $this->isLastOwner($user), 403, 'Paskutinio savininko teisių pašalinti negalima.');
        unset($data['role'], $data['password']);
        // Empty means keep the current secret; the UI never receives its old value.
        if (blank($data['website_password'] ?? null)) {
            unset($data['website_password']);
        }
        $user->update($data);
        $user->syncRoles([$role]);

        return redirect('/dashboard/users');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorizeTarget($request, $user);
        if ($user->id === $request->user()->id || $this->isLastOwner($user)) {
            return back()->with('error', 'Negalite ištrinti savo arba paskutinio savininko paskyros.');
        }
        $user->delete();

        return redirect('/dashboard/users');
    }

    public function show(Request $request, User $user)
    {
        $this->authorizeTarget($request, $user);

        return Inertia::render('Dashboard/Users/Show', [
            // Dedicated, password-confirmed administrator view only.
            'user' => $user->load('roles')->makeVisible('website_password'),
        ]);
    }
}
