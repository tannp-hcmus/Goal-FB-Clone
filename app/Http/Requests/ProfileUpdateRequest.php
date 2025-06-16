<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
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
        $userId = Auth::id();

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'regex:/^[\pL\s\-\'\.]+$/u',
                function ($attribute, $value, $fail) {
                    if (trim($value) === '') {
                        $fail('The name field cannot be empty or just whitespace.');
                    }
                }
            ],
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'description' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'], // max 2MB
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 50 characters.',
            'name.regex' => 'The name may only contain letters, spaces, hyphens, apostrophes, and periods.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'avatar.image' => 'The avatar must be an image.',
            'avatar.max' => 'The avatar may not be greater than 2MB.',
        ];
    }
}
