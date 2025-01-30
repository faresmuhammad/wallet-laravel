<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRecordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string',
            'sender_wallet' => 'integer|exists:wallets,id',
            'receiver_wallet' => 'integer|exists:wallets,id',
            'amount' => 'numeric',
            'date' => 'date|nullable',
            'labels' => 'array',
            'labels.*' => 'integer|exists:labels,id',
        ];
    }
}
