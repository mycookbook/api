<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\DisallowedCharactersRule;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:2', new DisallowedCharactersRule()],
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5',
        ];
    }
}
