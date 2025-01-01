<?php

namespace App\Transformer;

use App\DTO\BookOutput;
use App\Entity\Book;

/**
 * @implements Transformer<Book, BookOutput>
 */
class BookOutputTransformer implements Transformer
{

    /**
     * @param Book $data
     * @return BookOutput
     */
    public function transform($data): BookOutput
    {
        $result = new BookOutput();

        $result->uuid = $data->uuid->toString();
        $result->author = $data->author;
        $result->title = $data->title;
        $result->isbn = $data->isbn;
        $result->publishedDate = $data->publishedDate?->format('Y-m-d');

        return $result;
    }
}