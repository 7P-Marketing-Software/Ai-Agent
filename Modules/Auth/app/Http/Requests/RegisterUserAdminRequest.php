<?php

namespace Modules\Core\Auth\Http\Requests\AuthRequest;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserAdminRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users_admins,email',
            'phone' => ['required', 'string', 'regex:/^(010|011|012|015)\d{8}$/', 'unique:users_admins,phone'],
            'password' => 'required|string|min:8',
            'gender' => 'required|in:male,female',
        ];
    }


    public function authorize(): bool
    {
        return true;
    }
}
