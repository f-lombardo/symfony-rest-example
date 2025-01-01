<?php

namespace App\Service;

use App\DTO\BookOutput;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(title: 'Page')]
readonly class Page
{
    #[OA\Property(
        description: 'List of retrieved items',
        type: 'array',
        items: new OA\Items(ref: new Model(type: BookOutput::class))
    )]
    public array $items;

    #[OA\Property(description: 'Total number of items across all pages', example: 100)]
    public int $totalItems;

    #[OA\Property(description: 'Total number of pages', example: 10)]
    public int $totalPages;

    #[OA\Property(description: 'Current page number', example: 1)]
    public int $currentPage;

    #[OA\Property(description: 'Number of items per page', example: 10)]
    public int $itemsPerPage;

    /**
     * @param array<string|int, mixed> $items
     */
    public function __construct(array $items, int $totalItems, int $totalPages, int $currentPage, int $itemsPerPage)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
    }
}
