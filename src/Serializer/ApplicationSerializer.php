<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

readonly class ApplicationSerializer implements SerializerInterface
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [
                new ArrayDenormalizer(),
                new ObjectNormalizer(nameConverter: new CamelCaseToSnakeCaseNameConverter(), defaultContext: [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]),
            ],
            [
                new JsonEncoder(),
            ]
        );
    }

    final public function serialize(mixed $data, string $format = 'json', array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    final public function deserialize(mixed $data, string $type, string $format = 'json', array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
