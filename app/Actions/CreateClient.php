<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateClientData;
use App\Models\Client;

final readonly class CreateClient
{
    public function handle(CreateClientData $data): Client
    {
        return Client::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'status' => $data->status,
            'industry' => $data->industry,
            'contact_person' => $data->contact_person,
            'website' => $data->website,
        ]);
    }
}
