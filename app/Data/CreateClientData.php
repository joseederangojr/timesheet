<?php

declare(strict_types=1);

namespace App\Data;

final readonly class CreateClientData
{
    public function __construct(
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
