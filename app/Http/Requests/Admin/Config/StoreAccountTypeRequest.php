<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'at_desc' => ['required', 'string', 'max:255', 'unique:account_type,at_desc'],
        ];
    }

    public function messages(): array
    {
        return [
            'at_desc.required' => 'Account type name is required',
            'at_desc.max' => 'Account type name must not exceed 255 characters',
            'at_desc.unique' => 'This account type already exists',
        ];
    }
}
