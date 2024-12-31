<?php

namespace App\Transformer;


/**
 * @template T
 * @template Q
 */
interface Transformer
{
    /**
     * @param T $data
     * @return Q
     */
    public function transform($data);
}