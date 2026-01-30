<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.geographic.manage');
    }

    public function rules(): array
    {
        $areaId = $this->route('id');

        return [
            'a_desc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('area', 'a_desc')->ignore($areaId, 'a_id'),
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
            'a_desc.required' => 'Area name is required',
            'a_desc.unique' => 'An area with this name already exists',
            'stat_id.required' => 'Status is required',
            'stat_id.exists' => 'Invalid status selected',
        ];
    }
}
