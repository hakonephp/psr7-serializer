<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function strlen;

class Psr7Serializer
{
    public function serializeResponse(ResponseInterface $response): SerializableResponse
    {
        return new SerializableResponse(
            $response->getProtocolVersion(),
            $response->getHeaders(),
            $this->serializeStream($response->getBody(), true),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );
    }

    /**
     * @phpstan-return ($emptyAsNull is false ? SerializableStream : ?SerializableStream)
     */
    public function serializeStream(StreamInterface $stream, bool $emptyAsNull = false): ?SerializableStream
    {
        $contents = (string)$stream;

        if ($emptyAsNull) {
            return strlen($contents) === 0 ? null : new SerializableStream($contents);
        }

        return new SerializableStream($contents);
    }
}
