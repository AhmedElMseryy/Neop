<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    #-------------------------------------------------------RULES & MESSAGES
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'currency' => 'required|string|size:3|in:EGP,USD,EUR,SAR,AED',
            'balance' => 'nullable|numeric|min:0',
            'account_number' => 'nullable|string|unique:accounts,account_number',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User does not exist',
            'currency.required' => 'Currency is required',
            'currency.in' => 'Currency is not supported',
            'balance.min' => 'Balance cannot be negative',
            'account_number.unique' => 'Account number has already been taken',
        ];
    }
}
