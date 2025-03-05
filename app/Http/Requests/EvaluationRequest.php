<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
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
            'comment' => 'nullable|string',
            'note' => 'required|integer|min:1|max:5',
            'id_service_history'=>'required|exists:historique_services,id_service_history|unique:evaluations,service_history_id'
        ];
    }
}
