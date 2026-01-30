<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        $barangayId = $this->route('id');

        return [
            'b_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('barangay', 'b_desc')->ignore($barangayId, 'b_id'),
            ],
            'b_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('barangay', 'b_code')->ignore($barangayId, 'b_id'),
            ],
            'stat_id' => [
                'required',
                'exists:statuses,stat_id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'b_desc.required' => 'Barangay name is required',
            'b_desc.unique' => 'A barangay with this name already exists',
            'b_code.unique' => 'This barangay code is already in use',
            'stat_id.required' => 'Status is required',
            'stat_id.exists' => 'Invalid status selected',
        ];
    }
}
