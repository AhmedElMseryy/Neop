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
            'source_account_id.required' => 'Source account is required',
            'source_account_id.exists' => 'Source account does not exist',
            'destination_account_id.required' => 'Destination account is required',
            'destination_account_id.exists' => 'Destination account does not exist',
            'destination_account_id.different' => 'Cannot transfer to the same account',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be greater than zero',
        ];
    }
}
