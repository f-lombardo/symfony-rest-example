<?php

namespace App\Service;

readonly class Page
{
    /**
     * @param array<string|int, mixed> $items
     */
    public function __construct(public array $items, public int $totalItems, public int $totalPages, public int $currentPage, public int $itemsPerPage)
    {
    }
}