<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($this->route('user')),
            ],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => [$isCreate ? 'required' : 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
