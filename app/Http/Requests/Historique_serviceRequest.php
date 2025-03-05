<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Date;

class Historique_serviceRequest extends FormRequest
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
            'amount_paid' => 'required|integer',
            'hairstyle_type_id' => 'required|exists:type_coiffures,id_hairstyle_type',
            'employe_id' => 'required|exists:employes,id_employe',
            'salon_id' => 'required|exists:salons,id_salon',
            'date_rdv' => 'required|date',
            'heure_debut' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $currentHour = Date::now()->format('H:i');
                    if ($value > $currentHour) {
                        $fail('L\'heure de début ne peut pas être postérieure à l\'heure actuelle.');
                    }
                },
            ],
            'heure_fin' => [
                'required',
                'date_format:H:i',
                'after:heure_debut',
                function ($attribute, $value, $fail) {
                    $currentHour = Date::now()->format('H:i');
                    if ($value < $currentHour) {
                        $fail('L\'heure de fin ne peut pas être postérieure à l\'heure actuelle.');
                    }
                },
            ],
        ];
    }
}
