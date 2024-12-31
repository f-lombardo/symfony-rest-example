<?php

namespace App\DTO;

class BookOutput
{
    public string $uuid;
    public string $title;
    public string $author;
    public ?string $publishedDate = null;
    public ?string $isbn = null;
}