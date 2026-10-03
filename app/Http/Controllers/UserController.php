<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private RoleService $roleService
    ) {}

    public function index(IndexUserRequest $request)
    {
        $users = $this->userService->getUsers($request->filters(), $request->perPage());

        return Inertia::render('users/Index', [
            'users' => $users->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'created_at' => $user->created_at,
                'roles' => $user->roles->pluck('name')->toArray(),
            ]),
            'filters' => $request->filters(),
        ]);
    }

    public function create()
    {
        return Inertia::render('users/Create', [
            'roles' => $this->roleService->getAllRoles(),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            $this->userService->createUser($validated, $validated['roles'] ?? []);

            return redirect()->route('users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Gagal menambahkan pengguna: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal menambahkan pengguna: '.$e->getMessage())->withInput();
        }
    }

    public function edit(User $user)
    {
        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'roles' => $user->roles()->pluck('name')->toArray(),
            ],
            'roles' => $this->roleService->getAllRoles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        try {
            $this->userService->updateUser($user, $validated, $validated['roles'] ?? []);

            return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Gagal memperbarui pengguna: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal memperbarui pengguna: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->userService->deleteUser($user);

            return back()->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus pengguna: '.$e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Gagal menghapus pengguna: '.$e->getMessage());
        }
    }
}
