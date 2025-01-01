<?php

namespace App\DTO;

use Symfony\Component\Uid\Uuid;

readonly class NewObjectOutput
{
    public string $uuid;

    public function __construct(Uuid $uuidInput)
    {
        $this->uuid = $uuidInput->toString();
    }
}