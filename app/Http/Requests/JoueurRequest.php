<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoueurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $joueurId = $this->route('joueur');

        return [
            'pseudo' => [
                'required',
                'regex:/^[A-Za-z0-9_-]{3,15}$/',
                'max:15'
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                'unique:joueurs,email,' . $joueurId
            ],

            'score_total' => [
                'nullable',
                'integer',
                'min:0',
                'max:999999'
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pseudo.regex' => 'Le pseudo doit contenir 3 à 15 caractères (lettres, chiffres, - ou _)',
            'email.unique' => 'Cet email est déjà utilisé',
            'avatar.image' => 'Le fichier doit être une image',
        ];
    }
}
