<?php

namespace App\Http\Requests;

use App\Enums\StatutConcours;
use App\Models\Concours;
use App\Rules\EligibiliteConcours;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCandidatureRequest extends FormRequest
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
        $concours = Concours::findOrFail($this->input('concours_id'));

        return [
            'concours_id' => [
                'required',
                Rule::exists('concours', 'id')->where('statut', StatutConcours::Ouvert->value),
            ],
            'diplome_candidat' => [
                'required',
                'string',
                new EligibiliteConcours($concours, $this->user()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'concours_id.exists' => "Ce concours n'est pas ouvert aux inscriptions.",
        ];
    }
}
