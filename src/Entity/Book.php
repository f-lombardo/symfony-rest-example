<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private UuidInterface $uuid;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $author = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $published_date = null;

    #[ORM\Column(length: 17, nullable: true)]
    private ?string $isbn = null;

    public function __construct(?string $uuidString = null)
    {
        if (null === $uuidString) {
            $this->uuid = Uuid::uuid4();
        } else {
            $this->uuid = Uuid::fromString($uuidString);
        }
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedDate(): ?\DateTimeImmutable
    {
        return $this->published_date;
    }

    public function setPublishedDate(?\DateTimeImmutable $published_date): static
    {
        $this->published_date = $published_date;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }
}
