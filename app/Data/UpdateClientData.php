<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Client;

final readonly class UpdateClientData
{
    public function __construct(
        public Client $client,
        public string $name,
        public string $email,
        public ?string $phone,
        public ?string $address,
        public string $status,
        public ?string $industry,
        public ?string $contact_person,
        public ?string $website,
    ) {
        //
    }
}
