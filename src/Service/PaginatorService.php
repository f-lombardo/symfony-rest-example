<?php

namespace App\Service;

use App\Transformer\Transformer;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorService
{
    public const ITEMS_PER_PAGE = 30;
    public const MAX_ITEMS_PER_PAGE = 100;

    public function paginate(QueryBuilder $queryBuilder, int $currentPage = 1, int $itemsPerPage = self::ITEMS_PER_PAGE, ?Transformer $transformer = null): array
    {
        $this->checkValid($currentPage, $itemsPerPage);

        $paginator = new Paginator($queryBuilder);

        $paginator->getQuery()
            ->setFirstResult(($currentPage - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $totalItems = $paginator->count();
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        $items = $paginator->getIterator()->getArrayCopy();

        if (null !== $transformer) {
            $items = \array_map(fn ($from) => $transformer->transform($from), $items);
        }

        return [
            'items' => $items,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'current_page' => $currentPage,
            'items_per_page' => $itemsPerPage,
        ];
    }

    public function checkValid(int $currentPage, int $itemsPerPage): void
    {
        if ($currentPage < 1) {
            throw new \InvalidArgumentException('Current page must be greater than 0');
        }

        if ($itemsPerPage < 1 || $itemsPerPage > self::MAX_ITEMS_PER_PAGE) {
            throw new \InvalidArgumentException('Max items must be greater than 1 and less than or equal to ' . self::MAX_ITEMS_PER_PAGE);
        }
    }
}