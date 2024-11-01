<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'name' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'nullable|date',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
           'date' => $this->date ?? now()
        ]);
    }

}
