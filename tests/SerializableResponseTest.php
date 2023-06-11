<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class SerializableResponseTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers SerializableStream
     */
    public function test(): void
    {
        $subject = new SerializableResponse(
            '9.9',
            [
                'Foo' => ['Bar', 'Bar'],
            ],
            new SerializableStream('Foobar'),
            111,
            'Incomprehensible'
        );

        $expected = [
            'version' => '9.9',
            'headers' => [
                'Foo' => ['Bar', 'Bar'],
            ],
            'body' => new SerializableStream('Foobar'),
            'code' => 111,
            'reasonPhrase' => 'Incomprehensible',
        ];

        $this->assertEquals($expected, $subject->__serialize());

        $stream = Psr17FactoryDiscovery::findStreamFactory()->createStream('Foobar');
        $streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $streamFactory->createStream('Foobar')->willReturn($stream);

        $response = Psr17FactoryDiscovery::findResponseFactory()->createResponse(111, 'Incomprehensible');
        $responseFactory = $this->prophesize(ResponseFactoryInterface::class);
        $responseFactory->createResponse(111, 'Incomprehensible')->willReturn($response);
        $expected_response = $response
            ->withProtocolVersion('9.9')
            ->withHeader('Foo', ['Bar', 'Bar'])
            ->withBody($stream);

        $actual = $subject->toResponse($responseFactory->reveal(), $streamFactory->reveal());

        $this->assertEquals($expected_response, $actual);
    }
}
