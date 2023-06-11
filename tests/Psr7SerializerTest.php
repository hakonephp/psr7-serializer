<?php

declare(strict_types=1);

namespace Hakone\Psr7Serializer;

use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function base64_decode;
use function fopen;
use function serialize;
use function unserialize;

/**
 * @phpstan-import-type serialized_response_array from SerializableResponse
 * @phpstan-import-type serialized_stream_array from SerializableStream
 */
class Psr7SerializerTest extends TestCase
{
    /** @var Psr7Serializer */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Psr7Serializer();
    }

    /**
     * @covers Psr7Serializer::serializeResponse()
     * @dataProvider responseProvider
     * @phpstan-param serialized_response_array $expected
     */
    public function test_serializeResponse(ResponseInterface $response, array $expected): void
    {
        $actual = $this->subject->serializeResponse($response);

        $this->assertEquals($expected, $actual->__serialize());

        $serialized = serialize($actual);
        $unserialized = unserialize($serialized);

        $this->assertInstanceof(SerializableResponse::class, $unserialized);
        $this->assertEquals($expected, $unserialized->__serialize());
    }

    /**
     * @covers Psr7Serializer::serializeStream()
     * @dataProvider streamProvider
     * @phpstan-param ?serialized_response_array $expected
     */
    public function test_serializeStream(StreamInterface $response, bool $emptyAsNull, ?array $expected): void
    {
        $actual = $this->subject->serializeStream($response, $emptyAsNull);

        if ($expected === null) {
            $this->assertNull($actual);
        } else {
            $this->assertNotNull($actual);
            $this->assertEquals($expected, $actual->__serialize());

            $serialized = serialize($actual);
            $unserialized = unserialize($serialized);

            $this->assertInstanceOf(SerializableStream::class, $unserialized);
            $this->assertEquals($expected, $unserialized->__serialize());
        }
    }

    /**
     * @return iterable<array{ResponseInterface, serialized_response_array}>
     */
    public static function responseProvider(): iterable
    {
        $responseFactory = Psr17FactoryDiscovery::findResponseFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        yield [
            $responseFactory->createResponse(),
            [
                'version' => '1.1',
                'headers' => [],
                'body' => null,
                'code' => 200,
                'reasonPhrase' => 'OK',
            ],
        ];

        yield [
            $responseFactory->createResponse()->withHeader('Content-Type', 'application/json'),
            [
                'version' => '1.1',
                'headers' => [
                    'Content-Type' => ['application/json'],
                ],
                'body' => null,
                'code' => 200,
                'reasonPhrase' => 'OK',
            ],
        ];

        yield [
            $responseFactory->createResponse(404)->withBody(
                $streamFactory->createStream('<html></html>')
            ),
            [
                'version' => '1.1',
                'headers' => [],
                'body' => new SerializableStream('<html></html>'),
                'code' => 404,
                'reasonPhrase' => 'Not Found',
            ],
        ];

        yield [
            $responseFactory->createResponse(404, 'Not Foooooound'),
            [
                'version' => '1.1',
                'headers' => [],
                'body' => null,
                'code' => 404,
                'reasonPhrase' => 'Not Foooooound',
            ],
        ];
    }

    /**
     * @return iterable<array{0: StreamInterface, emptyAsNull: bool, 1: ?serialized_stream_array}>
     */
    public static function streamProvider(): iterable
    {
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        yield [
            $streamFactory->createStream(''),
            'emptyAsNull' => true,
            null,
        ];

        yield [
            $streamFactory->createStream(''),
            'emptyAsNull' => false,
            [
                'contents' => '',
            ],
        ];

        $gif = fopen('data://image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==', 'r');
        assert($gif !== false);
        $gif_bin = base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');

        foreach ([true, false] as $emptyAsNull) {
            yield [
                $streamFactory->createStream("\0"),
                'emptyAsNull' => $emptyAsNull,
                [
                    'contents' => "\0",
                ],
            ];

            yield [
                $streamFactory->createStream('<html></html>'),
                'emptyAsNull' => $emptyAsNull,
                [
                    'contents' => '<html></html>',
                ],
            ];

            yield [
                $streamFactory->createStreamFromResource($gif),
                'emptyAsNull' => $emptyAsNull,
                [
                    'contents' => $gif_bin,
                ],
            ];
        }
    }
}
