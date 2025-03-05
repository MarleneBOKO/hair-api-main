<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
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
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'reorder_level' => 'nullable|integer',
            'description' => 'nullable|string',
            'addition_date' => 'required|date',
            'last_modification_date' => 'required|date',
            'salon_id' => 'required|exists:salons,id_salon',
            'fournisseur_id' => 'required|exists:fournisseurs,id_supplier',
        ];
    }
}
