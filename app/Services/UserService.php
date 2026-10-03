<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

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

    public function getUserOptions(): Collection
    {
        return $this->userRepository->getUserOptions();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function createUser(array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($data, $roles) {
            $userData = collect($data)->except('roles')->toArray();

            $user = $this->userRepository->create($userData);

            if (! $user) {
                throw new \Exception('Gagal menyimpan data pengguna baru ke database.');
            }

            if (! empty($roles)) {
                $user->syncRoles($roles);
            }

            $this->activityLogService->log(
                'Create User',
                "Created a new user: {$user->name} ({$user->username})",
                ['user_id' => $user->id, 'roles' => $roles]
            );

            // Bersihkan cache permission secara eksplisit
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return $user->load('roles');
        });
    }

    public function updateUser(User $user, array $data, array $roles = []): bool
    {
        return DB::transaction(function () use ($user, $data, $roles) {
            $existingUser = $this->userRepository->findById($user->id);
            if (! $existingUser) {
                throw new \Exception('Data pengguna tidak ditemukan di sistem.');
            }

            $oldData = $existingUser->only(['name', 'username']);
            $userData = collect($data)->except('roles')->toArray();

            if (empty($userData['password'])) {
                unset($userData['password']);
            }

            $updated = $this->userRepository->update($existingUser, $userData);

            if (! $updated) {
                throw new \Exception('Gagal memperbarui data pengguna ke database.');
            }

            // Selalu sync roles - pastikan $roles adalah array murni
            $existingUser->syncRoles(array_values($roles));

            $this->activityLogService->log(
                'Update User',
                "Updated user: {$existingUser->name}",
                ['user_id' => $existingUser->id, 'old' => $oldData, 'new' => $existingUser->only(['name', 'username']), 'roles' => $roles]
            );

            // Bersihkan cache permission secara eksplisit
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $existingUser->load('roles');

            return true;
        });
    }

    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            $existingUser = $this->userRepository->findById($user->id);
            if (! $existingUser) {
                throw new \Exception('Data pengguna tidak ditemukan di sistem.');
            }

            if ($existingUser->id === auth()->id()) {
                throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri.');
            }

            $userName = $existingUser->name;
            $userId = $existingUser->id;

            $deleted = $this->userRepository->delete($existingUser);

            if (! $deleted) {
                throw new \Exception('Gagal menghapus data pengguna dari database.');
            }

            $this->activityLogService->log(
                'Delete User',
                "Deleted user: {$userName}",
                ['deleted_user_id' => $userId]
            );

            return true;
        });
    }
}
