<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConcoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('concours', 'code')->ignore($this->route('concours'))],
            'cycle' => ['required', Rule::in(['CAP/PL', 'CAP/PC', 'CAP/IFPB', 'CAP/IAFPB'])],
            'filiere' => ['required', Rule::in(['Tertiaire', 'Industriel', 'Agricole'])],
            'diplome_requis' => ['required', 'string', 'max:255'],
            'age_min' => ['nullable', 'integer', 'min:0', 'max:100'],
            'age_max' => ['nullable', 'integer', 'min:0', 'max:100', 'gte:age_min'],
            'frais_inscription' => ['required', 'numeric', 'min:0'],
            'frais_visite_medicale' => ['required', 'numeric', 'min:0'],
            'date_ouverture' => ['required', 'date'],
            'date_cloture' => ['required', 'date', 'after:date_ouverture'],
            'date_concours' => ['nullable', 'date', 'after:date_cloture'],
            'description' => ['nullable', 'string'],
        ];
    }
}
