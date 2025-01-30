<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatisticRequest extends FormRequest
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
            'name' => 'string',
            'type' => 'string|in:sum,average',
            'start_date' => 'date|date_format:Y-m-d|nullable',
            'end_date' => 'date|date_format:Y-m-d|after:start_date|nullable',
            'show_by' => 'string',
            'filter_by' => 'string'
        ];
    }
}
