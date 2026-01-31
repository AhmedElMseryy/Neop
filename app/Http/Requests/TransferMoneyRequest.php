<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferMoneyRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    #-------------------------------------------------------RULES & MESSAGES
    public function rules(): array
    {
        return [
            'source_account_id' => 'required|exists:accounts,id',
            'destination_account_id' => 'required|exists:accounts,id|different:source_account_id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'source_account_id.required' => 'حساب المرسل مطلوب',
            'source_account_id.exists' => 'حساب المرسل غير موجود',
            'destination_account_id.required' => 'حساب المستقبل مطلوب',
            'destination_account_id.exists' => 'حساب المستقبل غير موجود',
            'destination_account_id.different' => 'لا يمكن التحويل لنفس الحساب',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
        ];
    }
}
