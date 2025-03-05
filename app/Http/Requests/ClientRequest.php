<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'first_visit_date' => 'nullable|date',
            'last_visit_date' => 'nullable|date',

        ];
    }
}
