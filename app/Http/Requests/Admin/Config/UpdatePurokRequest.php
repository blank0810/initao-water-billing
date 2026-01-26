<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'p_desc' => ['sometimes', 'required', 'string', 'max:255'],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:statuses,stat_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'p_desc.required' => 'Purok name is required',
            'p_desc.max' => 'Purok name must not exceed 255 characters',
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
