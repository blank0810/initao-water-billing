<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;

class StoreChargeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:ChargeItem,name'],
            'code' => ['required', 'string', 'max:50', 'unique:ChargeItem,code'],
            'description' => ['nullable', 'string'],
            'default_amount' => ['required', 'numeric', 'min:0'],
            'charge_type' => ['required', 'string', 'in:one_time,recurring,per_unit'],
            'is_taxable' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Charge item name is required',
            'name.unique' => 'This charge item name already exists',
            'code.required' => 'Charge item code is required',
            'code.unique' => 'This charge item code already exists',
            'default_amount.required' => 'Default amount is required',
            'default_amount.min' => 'Default amount must be greater than or equal to 0',
            'charge_type.required' => 'Charge type is required',
            'charge_type.in' => 'Charge type must be one_time, recurring, or per_unit',
        ];
    }
}
