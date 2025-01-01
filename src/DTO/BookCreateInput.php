<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BookCreateInput
{
    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $title;
    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $author;
    #[Assert\Date]
    public ?string $publishedDate = null;
    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $isbn;
}