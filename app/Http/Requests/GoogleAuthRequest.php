<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleAuthRequest extends FormRequest
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
            'id_token' => 'required|string',
            'role'     => 'required|in:user,reward_partner',
        ];
    }

        public function messages(): array
    {
        return [
            'role.required' => 'Please select a role (user or reward partner)',
            'role.in' => 'Role must me wither user or reward_partner'
        ];
    }
}
