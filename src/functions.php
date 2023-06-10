<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Http\Discovery\Psr17FactoryDiscovery;
use LogicException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use function class_exists;
use function get_class;
use function serialize;
use function unserialize;

function unserializeResponse(
    string $data,
    ResponseFactoryInterface $responseFactory = null,
    StreamFactoryInterface $streamFactory = null
): ResponseInterface {
    $response = unserialize($data);

    if ($response === false || !$response instanceof SerializableResponse) {
        throw new LogicException('Data was not a Hakone\Psr7Serializer\SerializableResponse.');
    }

    if (($responseFactory === null || $streamFactory === null) &&
        !class_exists(Psr17FactoryDiscovery::class)
    ) {
        throw new LogicException('Missing required HTTP Factory implementation. Either pass ResponseFactory and StreamFactory explicitly or install php-http/discovery package.');
    }

    return $response->toResponse(
        $responseFactory ?? Psr17FactoryDiscovery::findResponseFactory(),
        $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory()
    );
}

/**
 * @param ResponseInterface|StreamInterface $object
 * @phpstan-return non-empty-string
 */
function serializePsr7(object $object): string
{
    return serialize(toSerializable($object));
}

/**
 * @param ResponseInterface|StreamInterface $object
 * @phpstan-return (
 *     $object is ResponseInterface ? SerializableResponse :
 *     $object is StreamInterface ? SerializableStream : never
 * )
 */
function toSerializable(object $object)
{
    $serializer = new Psr7Serializer();

    if ($object instanceof ResponseInterface) {
        return $serializer->serializeResponse($object);
    }

    if ($object instanceof StreamInterface) {
        return $serializer->serializeStream($object);
    }

    $class = get_class($object); // @phpstan-ignore-line

    throw new LogicException("Given unexpected {$class} object");
}
