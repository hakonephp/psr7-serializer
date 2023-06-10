<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @phpstan-type serialized_stream_array array{
 *     contents: string
 * }
 */
class SerializableStream
{
    /** @var string */
    private $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    /**
     * @phpstan-return serialized_stream_array
     */
    public function __serialize(): array
    {
        return [
            'contents' => $this->contents,
        ];
    }

    /**
     * @phpstan-param serialized_stream_array $data
     */
    public function __unserialize(array $data): void
    {
        $this->contents = $data['contents'];
    }

    public function toStream(StreamFactoryInterface $streamFactory): StreamInterface
    {
        return $streamFactory->createStream(
            $this->contents
        );
    }
}
