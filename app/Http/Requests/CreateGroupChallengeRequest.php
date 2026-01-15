<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupChallengeRequest extends FormRequest
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
            'challenge_name'     => 'required|string|max:255',
            'challenge_type_id'  => 'required|exists:challenge_types,id',
            'habits_id'          => 'required|array|min:1',
            'habits_id.*'        => 'exists:habits,id',
            'duraction'          => 'required|integer|in:7,10,30',
        ];
    }

}
