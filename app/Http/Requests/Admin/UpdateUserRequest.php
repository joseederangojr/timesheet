<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Queries\CheckUserIsAdminQuery;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && app(CheckUserIsAdminQuery::class)->handle($user);
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = $this->route('user');
        $userId = $user->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$userId],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'roles.required' => 'At least one role is required.',
            'roles.array' => 'Roles must be an array.',
            'roles.min' => 'At least one role must be selected.',
            'roles.*.exists' => 'Selected role does not exist.',
        ];
    }
}
