<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_name' => 'required|string|max:50|unique:roles,role_name|regex:/^[a-z_]+$/',
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
