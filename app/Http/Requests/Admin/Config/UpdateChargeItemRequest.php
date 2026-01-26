<?php

namespace App\Http\Requests\Admin\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChargeItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('config.billing.manage');
    }

    public function rules(): array
    {
        $chargeItemId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('ChargeItem', 'name')->ignore($chargeItemId, 'charge_item_id'),
            ],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('ChargeItem', 'code')->ignore($chargeItemId, 'charge_item_id'),
            ],
            'description' => ['sometimes', 'nullable', 'string'],
            'default_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'charge_type' => ['sometimes', 'required', 'string', 'in:one_time,recurring,per_unit'],
            'is_taxable' => ['sometimes', 'boolean'],
            'stat_id' => ['sometimes', 'required', 'integer', 'exists:statuses,stat_id'],
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
            'stat_id.exists' => 'Selected status does not exist',
        ];
    }
}
