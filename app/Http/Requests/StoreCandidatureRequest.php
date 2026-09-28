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
        $concours = Concours::find($this->input('concours_id'));

        return [
            'concours_id' => [
                'required',
                Rule::exists('concours', 'id')->where('statut', StatutConcours::Ouvert->value),
                Rule::unique('candidatures', 'concours_id')->where('user_id', $this->user()->id),
            ],
            'diplome_candidat' => array_filter([
                'required',
                'string',
                $concours ? new EligibiliteConcours($concours, $this->user()) : null,
            ]),
        ];
    }

    public function messages(): array
    {
        return [
            'concours_id.exists' => "Ce concours n'est pas ouvert aux inscriptions.",
            'concours_id.unique' => 'Vous avez déjà une candidature pour ce concours.',
        ];
    }
}
