<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StorePurokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'p_desc' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'p_desc.required' => 'Purok name is required',
            'p_desc.max' => 'Purok name must not exceed 255 characters',
        ];
    }
}
