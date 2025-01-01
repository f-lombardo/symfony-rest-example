<?php

namespace App\Service;

use App\Transformer\Transformer;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Traversable;

class PaginatorService
{
    public const int ITEMS_PER_PAGE = 30;
    public const int MAX_ITEMS_PER_PAGE = 100;

    /**
     * @template T
     * @template Q
     * @param Transformer<T, Q>|null $transformer
     */
    public function paginate(QueryBuilder $queryBuilder, int $currentPage = 1, int $itemsPerPage = self::ITEMS_PER_PAGE, ?Transformer $transformer = null): Page
    {
        $this->checkValid($currentPage, $itemsPerPage);

        $paginator = new Paginator($queryBuilder);

        $paginator->getQuery()
            ->setFirstResult(($currentPage - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);

        $totalItems = $paginator->count();
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        $items = \iterator_to_array($paginator->getIterator());

        if (null !== $transformer) {
            $items = \array_map(fn ($from) => $transformer->transform($from), $items);
        }

        return new Page(
            items: $items,
            totalItems: $totalItems,
            totalPages: $totalPages,
            currentPage: $currentPage,
            itemsPerPage: $itemsPerPage
        );
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