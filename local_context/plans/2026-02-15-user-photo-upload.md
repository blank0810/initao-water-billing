# User Photo Upload Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Complete user photo/avatar upload — from database column to display everywhere (navbar, user list, modals, forms).

**Architecture:** Base64 image submitted with the user form, processed by `FileUploadService`, stored to `public/uploads/avatars/`, path saved in `users.photo_path`. `User::photo_url` accessor provides the URL with Initao logo fallback.

**Tech Stack:** Laravel 12, Alpine.js, Tailwind CSS, existing `FileUploadService`

---

## Task 1: Database Migration

**Files:**
- Create: `database/migrations/2026_02_15_000000_add_photo_path_to_users_table.php`

**Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
```

**Step 2: Run the migration**

Run: `php artisan migrate`
Expected: "DONE" with the migration applied successfully.

**Step 3: Commit**

```bash
git add database/migrations/2026_02_15_000000_add_photo_path_to_users_table.php
git commit -m "feat(users): add photo_path column to users table"
```

---

## Task 2: User Model — Add `photo_path` and `photo_url` Accessor

**Files:**
- Modify: `app/Models/User.php:23-30` (fillable array)
- Modify: `app/Models/User.php` (add accessor after casts method, ~line 53)

**Step 1: Add `photo_path` to `$fillable`**

In `app/Models/User.php`, update the `$fillable` array:

```php
protected $fillable = [
    'username',
    'name',
    'email',
    'password',
    'stat_id',
    'u_type',
    'photo_path',
];
```

**Step 2: Add the `photo_url` accessor**

Add this method after the `casts()` method (~line 53), before the `userType()` relationship:

```php
/**
 * Get the full URL for the user's photo, with Initao logo fallback.
 */
public function getPhotoUrlAttribute(): string
{
    if ($this->photo_path) {
        return asset($this->photo_path);
    }

    return asset('images/logo.png');
}
```

**Step 3: Commit**

```bash
git add app/Models/User.php
git commit -m "feat(users): add photo_path to fillable and photo_url accessor"
```

---

## Task 3: Request Validation — Add `avatar` Field

**Files:**
- Modify: `app/Http/Requests/User/StoreUserRequest.php:22-31` (rules method)
- Modify: `app/Http/Requests/User/UpdateUserRequest.php:25-42` (rules method)

**Step 1: Update `StoreUserRequest` rules**

Add `'avatar'` to the rules array in `app/Http/Requests/User/StoreUserRequest.php`:

```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:100', 'unique:users,username'],
        'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:8'],
        'role_id' => ['required', 'exists:roles,role_id'],
        'status_id' => ['required', 'exists:statuses,stat_id'],
        'meter_reader_areas' => ['nullable', 'array'],
        'meter_reader_areas.*' => ['exists:area,a_id'],
        'avatar' => ['nullable', 'string'],
    ];
}
```

**Step 2: Update `UpdateUserRequest` rules**

Add `'avatar'` to the rules array in `app/Http/Requests/User/UpdateUserRequest.php`:

```php
public function rules(): array
{
    $userId = $this->route('id');

    return [
        'name' => ['required', 'string', 'max:255'],
        'username' => [
            'required',
            'string',
            'max:100',
            Rule::unique('users', 'username')->ignore($userId),
        ],
        'email' => [
            'nullable',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($userId),
        ],
        'password' => ['nullable', 'string', 'min:8'],
        'role_id' => ['required', 'exists:roles,role_id'],
        'status_id' => ['required', 'exists:statuses,stat_id'],
        'avatar' => ['nullable', 'string'],
    ];
}
```

**Step 3: Commit**

```bash
git add app/Http/Requests/User/StoreUserRequest.php app/Http/Requests/User/UpdateUserRequest.php
git commit -m "feat(users): add avatar validation to store and update requests"
```

---

## Task 4: UserService — Handle Photo Upload in Create/Update

**Files:**
- Modify: `app/Services/Users/UserService.php:1-11` (imports)
- Modify: `app/Services/Users/UserService.php:12` (class definition — inject FileUploadService)
- Modify: `app/Services/Users/UserService.php:63-79` (createUser method)
- Modify: `app/Services/Users/UserService.php:84-110` (updateUser method)
- Modify: `app/Services/Users/UserService.php:145-164` (formatUserForResponse method)

**Step 1: Add FileUploadService import and constructor injection**

Add the import at top of file:

```php
use App\Services\FileUploadService;
```

Add constructor to the `UserService` class:

```php
class UserService
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}
```

**Step 2: Update `createUser()` to handle avatar**

Replace the `createUser` method:

```php
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
```

**Step 3: Update `updateUser()` to handle avatar**

Replace the `updateUser` method:

```php
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
```

**Step 4: Add `photo_url` to `formatUserForResponse()`**

Add `'photo_url'` to the return array in `formatUserForResponse()`:

```php
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
```

**Step 5: Commit**

```bash
git add app/Services/Users/UserService.php
git commit -m "feat(users): handle photo upload in UserService create/update"
```

---

## Task 5: Add User Form JS — Include Avatar in Submission

**Files:**
- Modify: `resources/js/data/user/add-user.js:301-318` (form submit handler — build data object)

**Step 1: Update the form submission to include avatar base64**

In `resources/js/data/user/add-user.js`, find the form submit handler where the `data` object is built (~line 311). Replace the data-building block:

```javascript
            const data = {
                name: fullName,
                username: formData.get('username'),
                email: formData.get('email'),
                password: formData.get('password'),
                role_id: formData.get('role_id'),
                status_id: formData.get('status_id'),
            };

            // Include avatar if preview exists (base64 from FileReader)
            const avatarPreview = addUserForm.__x?.$data?.avatarPreview
                || document.querySelector('[x-data]')?.__x?.$data?.avatarPreview;
            if (avatarPreview) {
                data.avatar = avatarPreview;
            }
```

**Important context:** The existing Blade template already reads the file with `FileReader.readAsDataURL()` and stores it in Alpine.js `avatarPreview`. This step grabs that base64 string and sends it to the backend.

**Step 2: Commit**

```bash
git add resources/js/data/user/add-user.js
git commit -m "feat(users): include avatar base64 in add user form submission"
```

---

## Task 6: Edit User Modal — Add Photo Upload UI and Submission

**Files:**
- Modify: `resources/views/components/ui/user/modals/edit-user.blade.php`

**Step 1: Add photo upload section and wire avatar into save**

Replace the entire file content. Key changes:
- Add avatar preview + upload/remove buttons inside the form (before Full Name field)
- Store avatar base64 in a variable (`editAvatarPreview`)
- Include it in `saveUser()` payload
- Pre-populate with existing `photo_url` when opening the modal

In `resources/views/components/ui/user/modals/edit-user.blade.php`, add avatar upload section inside the `<form>` tag, after the hidden `editUserId` input and before the Full Name field:

```html
            <!-- Avatar Upload -->
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div id="editAvatarContainer" class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                        <img id="editAvatarImg" src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                </div>
                <div>
                    <input type="file" id="editAvatar" accept="image/*" class="hidden"
                        onchange="handleEditAvatarChange(this)">
                    <label for="editAvatar" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg cursor-pointer transition-colors">
                        <i class="fas fa-upload mr-1.5"></i>Change Photo
                    </label>
                    <button type="button" id="editRemoveAvatarBtn" onclick="removeEditAvatar()" class="hidden ml-2 inline-flex items-center px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition-colors">
                        <i class="fas fa-times mr-1.5"></i>Remove
                    </button>
                </div>
            </div>
```

Then update the `<script>` section. Add these variables and functions:

```javascript
let editAvatarBase64 = null;

function handleEditAvatarChange(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            editAvatarBase64 = e.target.result;
            document.getElementById('editAvatarImg').src = editAvatarBase64;
            document.getElementById('editRemoveAvatarBtn').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeEditAvatar() {
    editAvatarBase64 = 'remove';
    document.getElementById('editAvatarImg').src = '{{ asset("images/logo.png") }}';
    document.getElementById('editAvatar').value = '';
    document.getElementById('editRemoveAvatarBtn').classList.add('hidden');
}
```

In `showEditUserModal(user)`, add avatar initialization:

```javascript
    // Set avatar
    editAvatarBase64 = null;
    const avatarImg = document.getElementById('editAvatarImg');
    avatarImg.src = user.photo_url || '{{ asset("images/logo.png") }}';
    document.getElementById('editAvatar').value = '';
    document.getElementById('editRemoveAvatarBtn').classList.add('hidden');
```

In `saveUser()`, add avatar to `userData` object (after the password block):

```javascript
    // Include avatar if changed
    if (editAvatarBase64 && editAvatarBase64 !== 'remove') {
        userData.avatar = editAvatarBase64;
    }
```

**Step 2: Commit**

```bash
git add resources/views/components/ui/user/modals/edit-user.blade.php
git commit -m "feat(users): add photo upload to edit user modal"
```

---

## Task 7: View User Modal — Display Photo

**Files:**
- Modify: `resources/views/components/ui/user/modals/view-user.blade.php`

**Step 1: Add avatar display**

In `resources/views/components/ui/user/modals/view-user.blade.php`, replace the header icon div (the `w-12 h-12 bg-blue-100` div, lines 6-8):

```html
                <div class="flex-shrink-0 w-12 h-12 rounded-full overflow-hidden">
                    <img id="viewUserAvatar" src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="User avatar">
                </div>
```

In the `<script>` `showViewUserModal(user)` function, add:

```javascript
    document.getElementById('viewUserAvatar').src = user.photo_url || '{{ asset("images/logo.png") }}';
```

**Step 2: Commit**

```bash
git add resources/views/components/ui/user/modals/view-user.blade.php
git commit -m "feat(users): display photo in view user modal"
```

---

## Task 8: User List Table — Display Photo

**Files:**
- Modify: `resources/js/data/user/user.js:163-174` (nameCell rendering in renderTable)

**Step 1: Update the name cell to show user photo**

In `resources/js/data/user/user.js`, in the `renderTable()` function, replace the nameCell innerHTML block (~lines 164-174):

```javascript
            const nameCell = clone.querySelector('[data-col="name"]');
            nameCell.innerHTML = `
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                        <img src="${user.photo_url || '/images/logo.png'}" class="w-full h-full object-cover" alt="${user.name || 'User'}">
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${user.name || 'N/A'}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${user.email || 'N/A'}</div>
                    </div>
                </div>
            `;
```

**Step 2: Commit**

```bash
git add resources/js/data/user/user.js
git commit -m "feat(users): display photo in user list table"
```

---

## Task 9: Navbar — Display User Photo

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php:247-249` (user avatar in navbar dropdown button)

**Step 1: Replace the initial-letter circle with photo**

In `resources/views/layouts/navigation.blade.php`, find the avatar div (~line 247):

```html
                        <div class="h-8 w-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
```

Replace with:

```html
                        <div class="h-8 w-8 rounded-full overflow-hidden">
                            <img src="{{ $user->photo_url }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                        </div>
```

**Step 2: Commit**

```bash
git add resources/views/layouts/navigation.blade.php
git commit -m "feat(users): display photo in navbar"
```

---

## Task 10: Default Avatar Fix on Add Form

**Files:**
- Modify: `resources/views/user/add.blade.php:31-33` (default state when no preview)

**Step 1: Replace the Font Awesome icon default with Initao logo**

In `resources/views/user/add.blade.php`, find the template for when there's no avatar preview (~line 31-33):

```html
                                    <template x-if="!avatarPreview">
                                        <i class="fas fa-user text-5xl text-gray-400"></i>
                                    </template>
```

Replace with:

```html
                                    <template x-if="!avatarPreview">
                                        <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="Default avatar">
                                    </template>
```

**Step 2: Commit**

```bash
git add resources/views/user/add.blade.php
git commit -m "feat(users): use Initao logo as default avatar on add form"
```

---

## Task 11: Manual Testing Checklist

**No code changes — verify everything works end-to-end.**

**Step 1: Start dev server**

Run: `composer dev` (or `php artisan serve` + `npm run dev`)

**Step 2: Test add user with photo**
- Navigate to `/user/add`
- Verify Initao logo shows as default in the avatar circle
- Click "Upload Photo", select an image
- Verify preview updates
- Click "Remove", verify it reverts to Initao logo
- Fill out form, upload a photo, submit
- Verify success, redirected to user list

**Step 3: Test user list display**
- Navigate to `/user/list`
- Verify the newly created user shows their uploaded photo (not Font Awesome icon)
- Verify users without photos show the Initao logo

**Step 4: Test edit user modal**
- Click edit on the user with photo
- Verify their current photo shows in the modal
- Upload a new photo, save
- Verify the new photo shows in the table after refresh

**Step 5: Test view user modal**
- Click view on the user with photo
- Verify their photo shows in the modal header

**Step 6: Test navbar**
- Verify the logged-in user's photo (or Initao logo fallback) shows in the top-right navbar

**Step 7: Final commit**

If any fixes were needed during testing, commit them. Then:

```bash
git log --oneline -10
```

Verify the commit history looks clean.

---

## Files Modified (Summary)

| # | File | Change |
|---|------|--------|
| 1 | `database/migrations/2026_02_15_000000_add_photo_path_to_users_table.php` | **New** — add `photo_path` column |
| 2 | `app/Models/User.php` | Add `photo_path` to fillable, add `photo_url` accessor |
| 3 | `app/Http/Requests/User/StoreUserRequest.php` | Add `avatar` validation rule |
| 4 | `app/Http/Requests/User/UpdateUserRequest.php` | Add `avatar` validation rule |
| 5 | `app/Services/Users/UserService.php` | Inject FileUploadService, handle photo in create/update, add `photo_url` to API response |
| 6 | `resources/js/data/user/add-user.js` | Include avatar base64 in form submission |
| 7 | `resources/views/components/ui/user/modals/edit-user.blade.php` | Add photo upload UI + wire into save |
| 8 | `resources/views/components/ui/user/modals/view-user.blade.php` | Display user photo |
| 9 | `resources/js/data/user/user.js` | Display photo in user list table |
| 10 | `resources/views/layouts/navigation.blade.php` | Display photo in navbar |
| 11 | `resources/views/user/add.blade.php` | Use Initao logo as default avatar |
