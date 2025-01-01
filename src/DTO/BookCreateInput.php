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
    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $publishedDate;

    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $isbn;
}
