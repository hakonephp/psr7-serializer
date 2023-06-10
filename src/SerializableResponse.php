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
class SerializableResponse
{
    /** @var string */
    private $version;

    /** @var array<array<string>> */
    private $headers;

    /** @var ?SerializableStream */
    private $body;

    /** @var int */
    private $code;

    /** @var string */
    private $reasonPhrase;

    /**
     * @param array<array<string>> $headers
     */
    public function __construct(
        string $version,
        array $headers,
        ?SerializableStream $body,
        int $code,
        string $reasonPhrase
    ) {
        $this->version = $version;
        $this->headers = $headers;
        $this->body = $body;
        $this->code = $code;
        $this->reasonPhrase = $reasonPhrase;
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
