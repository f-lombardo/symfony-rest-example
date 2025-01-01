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
    #[ORM\Column(type: 'uuid', nullable: false)]
    public Uuid $uuid {
        get {
            return $this->uuid;
        }
    }

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    public string $title {
        get {
            return $this->title;
        }
        set {
            $this->title = $value;
        }
    }

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    public string $author {
        get {
            return $this->author;
        }
        set {
            $this->author = $value;
        }
    }

    #[ORM\Column(nullable: false)]
    public \DateTimeImmutable $publishedDate {
        get {
            return $this->publishedDate;
        }
        set {
            $this->publishedDate = $value;
        }
    }

    #[ORM\Column(length: 17, nullable: false)]
    public string $isbn {
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
