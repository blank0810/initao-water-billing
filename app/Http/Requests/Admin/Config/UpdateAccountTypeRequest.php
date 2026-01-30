<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        $accountTypeId = $this->route('id');

        return [
            'at_desc' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('account_type', 'at_desc')->ignore($accountTypeId, 'at_id'),
            ],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:statuses,stat_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'at_desc.required' => 'Account type name is required',
            'at_desc.max' => 'Account type name must not exceed 255 characters',
            'at_desc.unique' => 'This account type already exists',
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
