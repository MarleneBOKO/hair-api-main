<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeRequest extends FormRequest
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
            'skills' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'hiring_date' => 'required|date',
            'departure_date' => 'nullable|date',
            'work_hours' => 'required|string|max:255',
            'salary' => 'required|numeric',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:employes',
            
        ];
    }
}
