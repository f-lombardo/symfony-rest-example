<?php

namespace App\Controller;

use App\DTO\BookCreateInput;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Serializer\ApplicationSerializer;
use App\Service\PaginatorService;
use App\Transformer\BookOutputTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

class BookController extends AbstractController
{

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly PaginatorService $paginatorService,
        private readonly BookOutputTransformer $outputTransformer,
        private readonly ValidatorInterface $validator,
        private readonly ApplicationSerializer $serializer,
    )
    {
    }

    #[Route('/books', name: 'books_get_many', methods: ['GET'])]
    public function getMany(Request $request): JsonResponse
    {
        try {
            $currentPage = $request->query->getInt('page', 1);
            $itemsPerPage = $request->query->getInt('itemsPerPage', PaginatorService::ITEMS_PER_PAGE);

            $queryBuilder = $this->bookRepository->createQueryBuilder('b')
                ->orderBy('b.title', 'ASC')
                ->orderBy('b.publishedDate', 'DESC')
                ->orderBy('b.uuid', 'ASC');

            $paginatedData = $this->paginatorService->paginate($queryBuilder, $currentPage, $itemsPerPage, $this->outputTransformer);

            return $this->createJsonResponse($paginatedData);
        } catch (\InvalidArgumentException $e) {
           return $this->json($e->getMessage(), 400);
        }
    }

    #[Route('/books/{uuid}', name: 'books_get_one', requirements: ['uuid' => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$'], methods: ['GET'])]
    public function getOne(string $uuid): JsonResponse
    {
        $result = $this->bookRepository->find($uuid);

        if (!$result) {
            throw $this->createNotFoundException();
        }

        $output = $this->outputTransformer->transform($result);

        return $this->createJsonResponse($output);
    }

    #[Route('/books', name: 'books_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $content = $this->serializer->deserialize($content, BookCreateInput::class, 'json');
        $violations = $this->validator->validate($content);
        if (count($violations) > 0) {
            return $this->json($violations, 400);
        }

        $newBook = new Book();

        $newBook->isbn = $content->isbn;
        $newBook->title = $content->title;
        $newBook->author = $content->author;
        $newBook->publishedDate = $content->publishedDate;

    }

    public function createJsonResponse(mixed $output): JsonResponse
    {
        return new JsonResponse(data: $this->serializer->serialize($output, 'json'), json: true);
    }
}
