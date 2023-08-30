<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewRecordRequest extends FormRequest
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
            'amount' => 'numeric',
            'description' => 'nullable|string',
            'balance_id' => 'integer',
            'category_id' => 'integer',
            'wallet_id' => 'integer',
            'currency_id' => 'integer',
            'date' => 'date|nullable'
        ];
    }
}
