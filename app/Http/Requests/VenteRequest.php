<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenteRequest extends FormRequest
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
            'notes' => 'nullable|string',
            'date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
            'client_id' => 'required|exists:clients,id_client',
            'salon_id' => 'required|exists:salons,id_salon',
        ];
    
    }
}
