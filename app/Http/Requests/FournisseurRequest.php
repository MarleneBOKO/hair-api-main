<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FournisseurRequest extends FormRequest
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
            'supplier_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string|url',
            'contact' => 'nullable|string',
            'notes' => 'nullable|string',
            
        ];
    }
}
