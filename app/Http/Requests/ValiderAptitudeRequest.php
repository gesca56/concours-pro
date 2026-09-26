<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValiderAptitudeRequest extends FormRequest
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
            'aptitude_medicale' => ['required', Rule::in(['apte', 'inapte'])],
            'motif_inaptitude' => ['required_if:aptitude_medicale,inapte', 'nullable', 'string', 'max:500'],
        ];
    }
}
