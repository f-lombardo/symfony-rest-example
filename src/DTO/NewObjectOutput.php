<?php

namespace App\DTO;

use Symfony\Component\Uid\Uuid;

class NewObjectOutput
{
    public readonly string $uuid;
    public function __construct(Uuid $uuidInput)
    {
        $this->uuid = $uuidInput->toString();
    }
}