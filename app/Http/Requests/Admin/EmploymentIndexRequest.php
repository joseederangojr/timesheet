<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class EmploymentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', \App\Models\Employment::class) ?? false;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string'],
            'sort_direction' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,inactive,terminated'],
            'client' => ['nullable', 'string', 'exists:clients,name'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
