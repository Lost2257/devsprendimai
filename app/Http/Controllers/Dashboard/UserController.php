<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Users/Index', [
            'users' => User::with('roles')->latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Dashboard/Users/Create', [
            'roles' => Role::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required','email','unique:users'],
            'password' => ['required','min:8'],
            'role' => ['required'],
            'website_name' => ['nullable'],
            'website_login' => ['nullable'],
            'website_password' => ['nullable'],
            'services' => ['nullable'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'website_name' => $data['website_name'] ?? null,
            'website_login' => $data['website_login'] ?? null,
            'website_password' => $data['website_password'] ?? null,
            'services' => $data['services'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return redirect('/dashboard/users');
    }

    public function edit(User $user)
{
    return Inertia::render('Dashboard/Users/Edit', [
        'user' => $user->load('roles'),
        'roles' => Role::all()
    ]);
}

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required'],
            'website_name' => ['nullable'],
            'website_login' => ['nullable'],
            'website_password' => ['nullable'],
            'services' => ['nullable'],

        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'website_name' => $data['website_name'],
            'website_login' => $data['website_login'],
            'website_password' => $data['website_password'],
            'services' => $data['services'],
                ]);

        $user->syncRoles([$data['role']]);

        return redirect('/dashboard/users');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Negalite ištrinti savo paskyros');
        }

        $user->delete();

        return redirect('/dashboard/users');
    }

    public function show(User $user)
    {
        return Inertia::render('Dashboard/Users/Show', [
            'user' => $user->load('roles')
        ]);
    }
}