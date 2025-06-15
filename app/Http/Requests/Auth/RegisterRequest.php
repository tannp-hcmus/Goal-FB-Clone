<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:1900-01-01'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'firstname.required' => 'Please enter your first name.',
            'lastname.required' => 'Please enter your last name.',
            'gender.required' => 'Please select your gender.',
            'birthday.required' => 'Please select your birthday.',
            'birthday.before_or_equal' => 'Please select a valid birthday.',
            'birthday.after_or_equal' => 'Please select a valid birthday.',
            'email.required' => 'Please enter your email.',
            'email.unique' => 'This email is already in use.',
            'password.required' => 'Please enter your password.',
        ];
    }
}

