<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
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
            'name' => 'required|max:30',
            'target_amount' => 'required|numeric',
            'current_amount' => 'required|numeric',
            'period' => 'in:One Time,Week,Month,Year',
            'status' => 'in:Active,Finished,Not Started',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date',
            'type' => 'in:Master,Repeatable'
        ];
    }
}
