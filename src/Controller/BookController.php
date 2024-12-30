<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Service\PaginatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{

    public function __construct(private readonly BookRepository $bookRepository, private readonly PaginatorService $paginatorService)
    {
    }

    #[Route('/books', name: 'books_get', methods: ['GET'])]
    public function getMany(Request $request): JsonResponse
    {
        $currentPage = $request->query->getInt('page', 1);

        $queryBuilder = $this->bookRepository->createQueryBuilder('b')
            ->orderBy('b.title', 'ASC')
            ->orderBy('b.publishedDate', 'DESC')
            ->orderBy('b.uuid', 'ASC');

        $paginatedData = $this->paginatorService->paginate($queryBuilder, $currentPage);

        return $this->json($paginatedData);
    }
}
