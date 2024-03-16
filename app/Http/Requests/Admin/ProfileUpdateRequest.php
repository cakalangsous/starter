<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->route('user')->id)],
            'avatar' => 'image|file|max:2048',
            'password' => 'sometimes',
            'password_confirmation' => 'sometimes|same:password',
        ];

        if (!$this->route('user')->hasRole('Developer')) {
            $rules['roles'] = 'required';
        }
        return $rules;
    }
}
