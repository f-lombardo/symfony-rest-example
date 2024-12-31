<?php

namespace App\DTO;

class BookCreateInput
{
    public string $title;
    public string $author;
    public ?string $published_date = null;
    public ?string $isbn = null;
}