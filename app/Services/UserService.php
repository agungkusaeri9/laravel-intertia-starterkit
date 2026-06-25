<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ActivityLogService $activityLogService
    ) {}

    public function getUsers(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->getAllPaginated($filters, $perPage);
    }

    public function createUser(array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($data, $roles) {
            $userData = collect($data)->except('roles')->toArray();

            $user = User::create($userData);

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            $this->activityLogService->log(
                'Create User',
                "Created a new user: {$user->name} ({$user->username})",
                ['user_id' => $user->id, 'roles' => $roles]
            );

            // Bersihkan cache permission secara eksplisit
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return $user->load('roles');
        });
    }

    public function updateUser(User $user, array $data, array $roles = []): bool
    {
        return DB::transaction(function () use ($user, $data, $roles) {
            $oldData = $user->only(['name', 'username']);
            $userData = collect($data)->except('roles')->toArray();

            if (empty($userData['password'])) {
                unset($userData['password']);
            }

            $updated = $user->update($userData);

            // Selalu sync roles - pastikan $roles adalah array murni
            $user->syncRoles(array_values($roles));

            if ($updated) {
                $this->activityLogService->log(
                    'Update User',
                    "Updated user: {$user->name}",
                    ['user_id' => $user->id, 'old' => $oldData, 'new' => $user->only(['name', 'username']), 'roles' => $roles]
                );
            }

            // Bersihkan cache permission secara eksplisit
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $user->load('roles');

            return $updated;
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            if ($user->id === auth()->id()) {
                throw new \Exception('You cannot delete yourself.');
            }

            $userName = $user->name;
            $userId = $user->id;

            $deleted = $this->userRepository->delete($user);

            if ($deleted) {
                $this->activityLogService->log(
                    'Delete User',
                    "Deleted user: {$userName}",
                    ['deleted_user_id' => $userId]
                );
            }

            return $deleted;
        });
    }
}
