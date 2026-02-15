<?php

namespace App\Services\Users;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Get all users with their roles (paginated)
     */
    public function getAllUsers(int $perPage = 15, ?string $search = null, ?int $roleId = null): LengthAwarePaginator
    {
        $query = User::with(['roles', 'status'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($roleId) {
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.role_id', $roleId);
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get all users for API response (with roles)
     */
    public function getAllUsersForApi(): Collection
    {
        return User::with(['roles', 'status'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return $this->formatUserForResponse($user);
            });
    }

    /**
     * Get user by ID with roles
     */
    public function getUserById(int $id): ?User
    {
        return User::with(['roles', 'status'])->find($id);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($data['password']),
            'stat_id' => $data['status_id'],
        ];

        // Handle avatar upload
        if (! empty($data['avatar'])) {
            $result = $this->fileUploadService->storeBase64Image($data['avatar'], 'avatars');
            if ($result['success']) {
                $userData['photo_path'] = $result['path'];
            }
        }

        $user = User::create($userData);

        // Assign role
        if (isset($data['role_id'])) {
            $user->roles()->attach($data['role_id']);
        }

        return $user->load('roles', 'status');
    }

    /**
     * Update an existing user
     */
    public function updateUser(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'] ?? null,
        ];

        // Only update password if provided
        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Update status
        if (isset($data['status_id'])) {
            $updateData['stat_id'] = $data['status_id'];
        }

        // Handle avatar upload
        if (! empty($data['avatar'])) {
            $result = $this->fileUploadService->storeBase64Image($data['avatar'], 'avatars');
            if ($result['success']) {
                // Delete old photo if exists
                if ($user->photo_path) {
                    $this->fileUploadService->deleteFile($user->photo_path);
                }
                $updateData['photo_path'] = $result['path'];
            }
        }

        $user->update($updateData);

        // Sync role
        if (isset($data['role_id'])) {
            $user->roles()->sync([$data['role_id']]);
        }

        return $user->fresh(['roles', 'status']);
    }

    /**
     * Delete a user
     */
    public function deleteUser(User $user): array
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return [
                'success' => false,
                'message' => 'You cannot delete your own account',
            ];
        }

        // Prevent deletion of super admin
        if ($user->isSuperAdmin()) {
            return [
                'success' => false,
                'message' => 'Cannot delete super admin user',
            ];
        }

        $user->roles()->detach();
        $user->delete();

        return [
            'success' => true,
            'message' => 'User deleted successfully',
        ];
    }

    /**
     * Format user for API response
     */
    public function formatUserForResponse(User $user): array
    {
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'photo_url' => $user->photo_url,
            'role' => $role ? [
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'display_name' => ucwords(str_replace('_', ' ', $role->role_name)),
            ] : null,
            'status' => $user->status ? $user->status->stat_desc : 'Unknown',
            'status_id' => $user->stat_id,
            'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $user->created_at?->format('M d, Y'),
        ];
    }

    /**
     * Suggest available usernames based on first and last name.
     *
     * @return array<string> Available username suggestions
     */
    public function suggestUsernames(string $firstName, string $lastName): array
    {
        $first = strtolower(trim($firstName));
        $last = strtolower(str_replace(' ', '', trim($lastName)));
        $firstInitial = mb_substr($first, 0, 1);

        $candidates = [
            $first.'.'.$last,          // juan.delacruz
            $firstInitial.$last,        // jdelacruz
            $first.'_'.$last,           // juan_delacruz
            $last.'.'.$first,           // delacruz.juan
        ];

        $candidates = array_unique($candidates);

        $available = [];
        foreach ($candidates as $candidate) {
            if (! User::where('username', $candidate)->exists()) {
                $available[] = $candidate;
            }
        }

        // If all taken, append numbers to the first format
        if (empty($available)) {
            $base = $first.'.'.$last;
            $counter = 1;
            while (User::where('username', $base.$counter)->exists()) {
                $counter++;
            }
            $available[] = $base.$counter;
        }

        return $available;
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return [
            'total' => User::count(),
            'active' => User::where('stat_id', $activeStatusId)->count(),
            'inactive' => User::where('stat_id', '!=', $activeStatusId)->count(),
            'admins' => User::whereHas('roles', function ($q) {
                $q->whereIn('role_name', [Role::SUPER_ADMIN, Role::ADMIN]);
            })->count(),
        ];
    }
}
