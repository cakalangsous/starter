<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "title" => "sometimes",
            "slug" => "sometimes|unique:posts,slug," . $this->post->id,
            "body" => "sometimes",
            "category" => "sometimes",
            "tags" => "sometimes",
            "excerpt" => "sometimes",
            "meta_keywords" => "sometimes",
            "meta_description" => "sometimes",
            "publish_status" => "required",
            "allow_comments" => "sometimes"
        ];
    }
}
