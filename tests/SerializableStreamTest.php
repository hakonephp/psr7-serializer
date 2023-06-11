<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\StreamFactoryInterface;

class SerializableStreamTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers SerializableStream
     */
    public function test(): void
    {
        $subject = new SerializableStream('Foobar');

        $this->assertEquals([
            'contents' => 'Foobar',
        ], $subject->__serialize());

        $stream = Psr17FactoryDiscovery::findStreamFactory()->createStream('Foobar');

        $streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $streamFactory->createStream('Foobar')->willReturn($stream);

        $this->assertEquals('Foobar', (string)$subject->toStream($streamFactory->reveal()));
    }
}
