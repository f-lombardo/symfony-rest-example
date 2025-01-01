<?php

namespace App\Controller;

use App\DTO\BookCreateInput;
use App\DTO\BookOutput;
use App\DTO\BookUpdateInput;
use App\DTO\NewObjectOutput;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Serializer\ApplicationSerializer;
use App\Service\Page;
use App\Service\PaginatorService;
use App\Transformer\BookOutputTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly PaginatorService $paginatorService,
        private readonly BookOutputTransformer $outputTransformer,
        private readonly ValidatorInterface $validator,
        private readonly ApplicationSerializer $serializer,
    ) {
    }

    #[Route('/books', name: 'books_get_many', methods: ['GET'])]
    #[OA\Get(
        summary: 'List books',
        tags: ['Books']
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number for pagination',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: 1)
    )]
    #[OA\Parameter(
        name: 'itemsPerPage',
        description: 'Number of items per page',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'integer', default: PaginatorService::ITEMS_PER_PAGE)
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            ref: new Model(type: Page::class)
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request'
    )]
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
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/books/{uuid}', name: 'books_get_one', requirements: ['uuid' => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$'], methods: ['GET'])]
    #[OA\Get(
        summary: 'Retrieve a book by UUID',
        tags: ['Books']
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: BookOutput::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
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
    #[OA\Post(
        summary: 'Create a new book',
        tags: ['Books']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: BookCreateInput::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Successfully created book',
        content: new OA\JsonContent(ref: new Model(type: NewObjectOutput::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request',
        content: new OA\JsonContent()
    )]
    public function create(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $content = $this->serializer->deserialize($content, BookCreateInput::class, 'json');
        $violations = $this->validator->validate($content);
        if (\count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        $newBook = new Book();

        $newBook->isbn = $content->isbn;
        $newBook->title = $content->title;
        $newBook->author = $content->author;
        $newBook->publishedDate = new \DateTimeImmutable($content->publishedDate);

        $this->entityManager->persist($newBook);
        $this->entityManager->flush();

        return $this->createJsonResponse(new NewObjectOutput($newBook->uuid), Response::HTTP_CREATED);
    }

    #[Route('/books/{uuid}', name: 'books_delete', requirements: ['uuid' => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$'], methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Delete a book by UUID',
        tags: ['Books']
    )]
    #[OA\Response(
        response: 204,
        description: 'No content (book deleted successfully)',
        content: new OA\JsonContent()
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    public function delete(string $uuid): Response
    {
        $book = $this->bookRepository->find($uuid);

        if (!$book) {
            throw $this->createNotFoundException();
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/books/{uuid}', name: 'books_update', requirements: ['uuid' => '^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$'], methods: ['PUT'])]
    #[OA\Put(
        summary: 'Update a book by UUID',
        tags: ['Books']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: BookUpdateInput::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful update',
        content: new OA\JsonContent(ref: new Model(type: BookOutput::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request'
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found'
    )]
    public function update(Request $request, string $uuid): Response
    {
        $book = $this->bookRepository->find($uuid);

        if (!$book) {
            throw $this->createNotFoundException();
        }

        $content = $request->getContent();
        $content = $this->serializer->deserialize($content, BookUpdateInput::class, 'json');
        $violations = $this->validator->validate($content);
        if (\count($violations) > 0) {
            return $this->json($violations, Response::HTTP_BAD_REQUEST);
        }

        if (null !== $content->isbn) {
            $book->isbn = $content->isbn;
        }
        if (null !== $content->title) {
            $book->title = $content->title;
        }
        if (null !== $content->author) {
            $book->author = $content->author;
        }
        if (null !== $content->publishedDate) {
            $book->publishedDate = new \DateTimeImmutable($content->publishedDate);
        }

        $this->entityManager->flush();

        return new Response(status: Response::HTTP_OK);
    }

    private function createJsonResponse(mixed $output, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(data: $this->serializer->serialize($output, 'json'), status: $status, json: true);
    }
}
