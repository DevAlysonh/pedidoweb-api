<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerAddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'zipcode' => 'nullable|string|regex:/^\d{5}-?\d{3}$/|max:9'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('zipcode')) {
            $this->merge([
                'zipcode' => str_replace('-', '', $this->zipcode),
            ]);
        }
    }
}
