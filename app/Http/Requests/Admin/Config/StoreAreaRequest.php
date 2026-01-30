<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        return [
            'a_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('area', 'a_desc'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'a_desc.required' => 'Area name is required',
            'a_desc.unique' => 'An area with this name already exists',
        ];
    }
}
