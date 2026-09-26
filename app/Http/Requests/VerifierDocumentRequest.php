<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifierDocumentRequest extends FormRequest
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
            'statut_verification' => ['required', Rule::in(['valide', 'rejete'])],
            'motif_rejet' => ['required_if:statut_verification,rejete', 'nullable', 'string', 'max:500'],
        ];
    }
}
