<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordRequest extends FormRequest
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
            'name' => 'nullable|string',
            'category_id' => 'integer|nullable|exists:categories,id',
            'date' => 'date',
            'type' => 'string|in:Income,Expense,Transfer',
        ];
    }
}
