<?php

namespace App\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class BookCreateInput
{
    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $title;

    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $author;

    #[Assert\Date]
    #[Assert\NotBlank(options: ['allowNull' => false])]
    #[OA\Property(
        description: 'Publication date in YYYY-MM-DD format',
        type: 'string',
        format: 'date',
        example: '2023-01-31'
    )]
    public string $publishedDate;

    #[Assert\NotBlank(options: ['allowNull' => false])]
    public string $isbn;
}
