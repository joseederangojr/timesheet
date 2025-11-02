<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateClientData;
use App\Models\Client;

final readonly class UpdateClient
{
    public function handle(UpdateClientData $data): Client
    {
        $data->client->update([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'status' => $data->status,
            'industry' => $data->industry,
            'contact_person' => $data->contact_person,
            'website' => $data->website,
        ]);

        return $data->client->refresh();
    }
}
