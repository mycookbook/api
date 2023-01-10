<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeStoreRequest extends FormRequest
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
            'is_draft' => 'required|in:true,false',
            'name' => 'required|unique:recipes',
            'imgUrl' => 'required|url',
            'ingredients.*.name' => 'required',
            'ingredients.*.unit' => 'required',
            'description' => 'required',
            'cookbook_id' => 'required|exists:cookbooks,id',
            'summary' => 'required',
            'nationality' => 'required|exists:flags,flag',
            'cuisine' => 'required'
        ];
    }
}
