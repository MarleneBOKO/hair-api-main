<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalonRequest extends FormRequest
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

       // dd($this->all());
            return [
                'salon_name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => 'required|email|max:255|unique:salons',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'nullable|string|url',
                'creation_date' => 'required|date|before_or_equal:today',
                'last_update_date' => 'nullable|date|after_or_equal:creation_date',
                'longitude' =>'nullable|string|max:255',
                'latitude' => 'nullable|string|max:255',
                'percent' => 'required|numeric',
                'percent_cancel' => 'required|numeric',
                'heure_debut' => 'required|date_format:H:i',
                'heure_fin' => 'required|date_format:H:i'



            ];

    }
}
