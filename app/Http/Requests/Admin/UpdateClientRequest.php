<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', \App\Models\Client::class) ??
            false;
    }

    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        /** @var \App\Models\Client $client */
        $client = $this->route('client');
        $clientId = $client->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'unique:clients,email,'.$clientId,
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:active,inactive,prospect'],
            'industry' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.max' => 'Phone number must not exceed 20 characters.',
            'address.max' => 'Address must not exceed 1000 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active, inactive, or prospect.',
            'industry.max' => 'Industry must not exceed 255 characters.',
            'contact_person.max' => 'Contact person name must not exceed 255 characters.',
            'website.url' => 'Please enter a valid website URL.',
            'website.max' => 'Website URL must not exceed 255 characters.',
        ];
    }
}
