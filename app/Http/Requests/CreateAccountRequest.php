<?php

namespace App\Http\Requests;

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
            'user_id.required' => 'معرف المستخدم مطلوب',
            'user_id.exists' => 'المستخدم غير موجود',
            'currency.required' => 'العملة مطلوبة',
            'currency.in' => 'العملة غير مدعومة',
            'balance.min' => 'الرصيد لا يمكن أن يكون سالباً',
            'account_number.unique' => 'رقم الحساب مستخدم من قبل',
        ];
    }
}
