<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @phpstan-type serialized_response_array array{
 *     version: string,
 *     headers: array<array<string>>,
 *     body: ?SerializableStream,
 *     code: int,
 *     reasonPhrase: string
 * }
 */
readonly class SerializableResponse
{
    /**
     * @param array<array<string>> $headers
     */
    public function __construct(
        private string $version,
        private array $headers,
        private ?SerializableStream $body,
        private int $code,
        private string $reasonPhrase
    ) {
    }

    /**
     * @phpstan-return serialized_response_array
     */
    public function __serialize(): array
    {
        return [
            'version' => $this->version,
            'headers' => $this->headers,
            'body' => $this->body,
            'code' => $this->code,
            'reasonPhrase' => $this->reasonPhrase,
        ];
    }

    /**
     * @phpstan-param serialized_response_array $data
     */
    public function __unserialize(array $data): void
    {
        $this->version = $data['version'];
        $this->headers = $data['headers'];
        $this->body = $data['body'];
        $this->code = $data['code'];
        $this->reasonPhrase = $data['reasonPhrase'];
    }

    public function toResponse(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory): ResponseInterface
    {
        $response = $responseFactory
            ->createResponse($this->code, $this->reasonPhrase)
            ->withProtocolVersion($this->version);

        foreach ($this->headers as $name => $header) {
            $response = $response->withHeader($name, $header);
        }

        if ($this->body) {
            $response = $response->withBody($this->body->toStream($streamFactory));
        }

        return $response;
    }
}
