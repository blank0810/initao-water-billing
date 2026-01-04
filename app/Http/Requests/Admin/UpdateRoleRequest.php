<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasPermission('roles.manage');
    }

    public function rules(): array
    {
        $role = $this->route('role');
        $roleId = $role instanceof \App\Models\Role ? $role->role_id : $role;

        return [
            'role_name' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[a-z_]+$/',
                Rule::unique('roles', 'role_name')->ignore($roleId, 'role_id'),
            ],
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,permission_name',
        ];
    }

    public function messages(): array
    {
        return [
            'role_name.regex' => 'Role name must be lowercase letters and underscores only (e.g., custom_role)',
            'role_name.unique' => 'This role name already exists',
        ];
    }
}
