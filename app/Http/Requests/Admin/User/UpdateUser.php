<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->can('users.crud');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'patronymic' => ['nullable', 'string'],
            'email' => ['sometimes', 'email', 'string'],
            'password' => ['sometimes', 'confirmed', 'min:8', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9]).*$/', 'string'],
            'is_block' => ['sometimes', 'boolean'],
            'service_id' => ['sometimes'],
            'roles' => ['sometimes', 'array'],
            'services' => ['sometimes', 'array']
        ];
    }
}
