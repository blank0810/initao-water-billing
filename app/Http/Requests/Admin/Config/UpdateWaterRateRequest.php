<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWaterRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'period_id' => 'nullable|exists:period,per_id',
            'class_id' => 'required|exists:account_type,at_id',
            'range_id' => 'required|integer|min:1',
            'range_min' => 'required|numeric|min:0',
            'range_max' => 'required|numeric|gt:range_min',
            'rate_val' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Account type is required',
            'class_id.exists' => 'Invalid account type',
            'range_id.required' => 'Range tier is required',
            'range_min.required' => 'Minimum range is required',
            'range_max.required' => 'Maximum range is required',
            'range_max.gt' => 'Maximum range must be greater than minimum range',
            'rate_val.required' => 'Rate per cu.m. is required',
        ];
    }
}
