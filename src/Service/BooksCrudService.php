<?php

namespace App\Service;

use App\DTO\BookCreateInput;
use App\DTO\BookOutput;
use App\DTO\BookUpdateInput;
use App\Entity\Book;
use App\Exception\ValidationException;
use App\Repository\BookRepository;
use App\Transformer\BookOutputTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BooksCrudService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
        private readonly PaginatorService $paginatorService,
        private readonly BookOutputTransformer $outputTransformer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getMany(int $currentPage, int $itemsPerPage): Page
    {
        $queryBuilder = $this->bookRepository->createQueryBuilder('b')
            ->orderBy('b.uuid', 'ASC');

        return $this->paginatorService->paginate($queryBuilder, $currentPage, $itemsPerPage, $this->outputTransformer);
    }

    public function getOne(string $uuid): ?BookOutput
    {
        $result = $this->bookRepository->find($uuid);

        if (!$result) {
            return null;
        }

        return $this->outputTransformer->transform($result);
    }

    public function delete(string $uuid): bool
    {
        $book = $this->bookRepository->find($uuid);

        if (!$book) {
            return false;
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return true;
    }

    public function create(BookCreateInput $content): Book
    {
        $violations = $this->validator->validate($content);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $newBook = new Book();

        $newBook->isbn = $content->isbn;
        $newBook->title = $content->title;
        $newBook->author = $content->author;
        $newBook->publishedDate = new \DateTimeImmutable($content->publishedDate);

        $this->entityManager->persist($newBook);
        $this->entityManager->flush();

        return $newBook;
    }

    public function update(BookUpdateInput $content, Book $book): void
    {
        $violations = $this->validator->validate($content);
        if (\count($violations) > 0) {
            throw new ValidationException($violations);
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
    }
}
