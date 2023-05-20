<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\SupportedImageUrlFormatsRule;
use Illuminate\Foundation\Http\FormRequest;

class CookbookStoreRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required|min:126',
            'bookCoverImg' => ['required', new SupportedImageUrlFormatsRule()],
            'categories' => 'required',
            'flag_id' => 'required|exists:flags,flag',
        ];
    }
}
