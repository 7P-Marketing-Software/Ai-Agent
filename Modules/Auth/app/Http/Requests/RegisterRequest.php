<?php

namespace Modules\Auth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'string','unique:users,phone'],
            'password' => 'required|string|min:8',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
