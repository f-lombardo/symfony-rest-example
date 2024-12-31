<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    public Uuid $uuid {
        get {
            return $this->uuid;
        }
    }

    #[ORM\Column(type: Types::TEXT)]
    public string $title {
        get {
            return $this->title;
        }
        set {
            $this->title = $value;
        }
    }

    #[ORM\Column(type: Types::TEXT)]
    public string $author {
        get {
            return $this->author;
        }
        set {
            $this->author = $value;
        }
    }

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $published_date = null {
        get {
            return $this->published_date;
        }
        set {
            $this->published_date = $value;
        }
    }

    #[ORM\Column(length: 17, nullable: true)]
    public ?string $isbn = null {
        get {
            return $this->isbn;
        }
        set {
            $this->isbn = $value;
        }
    }

    public function __construct(?string $uuidString = null)
    {
        if (null === $uuidString) {
            $this->uuid = Uuid::v4();
        } else {
            $this->uuid = Uuid::fromString($uuidString);
        }
    }

}
