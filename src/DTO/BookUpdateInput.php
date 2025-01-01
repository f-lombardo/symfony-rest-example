<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BookUpdateInput
{
    #[Assert\NotBlank(options: ['allowNull' => true])]
    public ?string $title = null;

    #[Assert\NotBlank(options: ['allowNull' => true])]
    public ?string $author = null;

    #[Assert\Date]
    #[Assert\NotBlank(options: ['allowNull' => true])]
    public ?string $publishedDate = null;

    #[Assert\NotBlank(options: ['allowNull' => true])]
    public ?string $isbn = null;
}
