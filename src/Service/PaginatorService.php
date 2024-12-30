<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorService
{
    public const ITEMS_PER_PAGE = 30;

    public function paginate(QueryBuilder $queryBuilder, int $currentPage = 1): array
    {
        $paginator = new Paginator($queryBuilder);

        $paginator->getQuery()
            ->setFirstResult(($currentPage - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        $totalItems = $paginator->count();
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        return [
            'items' => $paginator->getIterator()->getArrayCopy(),
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
            'items_per_page' => self::ITEMS_PER_PAGE,
        ];
    }
}