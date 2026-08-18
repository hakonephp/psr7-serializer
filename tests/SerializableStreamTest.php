<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\StreamFactoryInterface;

#[CoversClass(SerializableStream::class)]
class SerializableStreamTest extends TestCase
{
    use ProphecyTrait;

    public function test(): void
    {
        $subject = new SerializableStream('Foobar');

        self::assertEquals([
            'contents' => 'Foobar',
        ], $subject->__serialize());

        $stream = Psr17FactoryDiscovery::findStreamFactory()->createStream('Foobar');

        $streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $streamFactory->createStream('Foobar')
            ->willReturn($stream);

        self::assertEquals('Foobar', (string)$subject->toStream($streamFactory->reveal()));
    }
}
