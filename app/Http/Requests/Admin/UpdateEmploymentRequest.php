<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateEmploymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('employment')) ??
            false;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'position' => ['required', 'string', 'max:255'],
            'hire_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'string', 'in:active,inactive,terminated'],
            'salary' => ['nullable', 'string', 'regex:/^\d+(\.\d{1,2})?$/'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'effective_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after:effective_date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Employee is required.',
            'user_id.exists' => 'Selected employee does not exist.',
            'client_id.exists' => 'Selected client does not exist.',
            'position.required' => 'Position is required.',
            'hire_date.required' => 'Hire date is required.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'status.required' => 'Employment status is required.',
            'status.in' => 'Invalid employment status.',
            'salary.regex' => 'Salary must be a valid decimal number.',
            'effective_date.required' => 'Effective date is required.',
            'end_date.after' => 'End date must be after the effective date.',
        ];
    }
}
