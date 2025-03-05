<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Rendez_vouRequest extends FormRequest
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
             'date_and_time' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
            'price' => 'required|numeric',
            'payment_method' => 'nullable|string',
            'usesOwnAccessories' =>'required',
            //'accessories' => 'required|array',
            //'transactionId' =>'required|string',
            //'accompte' => 'required|numeric',

        ];
    }
}
