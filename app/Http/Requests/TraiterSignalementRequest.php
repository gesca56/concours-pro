<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TraiterSignalementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reponse_administration' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reponse_administration.required' => 'Rédigez une réponse pour le candidat.',
            'reponse_administration.min' => 'La réponse doit contenir au moins 5 caractères.',
        ];
    }
}
