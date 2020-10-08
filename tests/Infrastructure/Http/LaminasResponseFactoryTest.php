<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Http\StreamFactory;
use MyOnlineStore\Common\Factory\Infrastructure\Http\LaminasResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class LaminasResponseFactoryTest extends TestCase
{
    /** @var LaminasResponseFactory */
    private $factory;

    /** @var StreamFactory */
    private $streamFactory;

    protected function setUp(): void
    {
        $this->factory = new LaminasResponseFactory(
            $this->streamFactory = $this->createMock(StreamFactory::class)
        );
    }

    public function testCreateJsonResponseWillReturnInstanceOfResponse(): void
    {
        $data = ['foo' => 'Twoich wyborów'];
        $headers = ['qux' => 'lax'];
        $response = $this->factory->createJsonResponse($data, 203, $headers, \JSON_PARTIAL_OUTPUT_ON_ERROR);

        self::assertSame($data, \json_decode($response->getBody()->getContents(), true));
        self::assertSame(203, $response->getStatusCode());
        self::assertEquals(['qux' => ['lax'], 'content-type' => ['application/json']], $response->getHeaders());
    }

    public function testCreateRedirectResponseWillReturnInstanceOfResponseWithGivenStatusCode(): void
    {
        $response = $this->factory->createRedirectResponse(
            $uri = 'http://foobar.baz',
            $statusCode = 303
        );

        self::assertSame($statusCode, $response->getStatusCode());
        self::assertSame([$uri], $response->getHeader('location'));
    }

    public function testCreateResponseFromStringWillReturnInstanceOfResponse(): void
    {
        $this->streamFactory->expects(self::once())
            ->method('createFromString')
            ->with('test')
            ->willReturn($stream = $this->createMock(StreamInterface::class));

        $response = $this->factory->createResponseFromString('test', 204, ['foo' => 'bar']);

        self::assertSame($stream, $response->getBody());
        self::assertSame(204, $response->getStatusCode());
        self::assertSame(['foo' => ['bar']], $response->getHeaders());
    }

    public function testCreateResponseFromStringWithBodyAndStatusCodeOnlyWillReturnInstanceOfResponse(): void
    {
        $stream = $this->createMock(StreamInterface::class);

        $this->streamFactory->expects(self::once())
            ->method('createFromString')
            ->with('test')
            ->willReturn($stream);

        $response = $this->factory->createResponseFromString('test', 204);

        self::assertSame($stream, $response->getBody());
        self::assertEquals(204, $response->getStatusCode());
        self::assertEquals([], $response->getHeaders());
    }

    public function testCreateResponseFromStringWithBodyOnlyWillReturnInstanceOfResponse(): void
    {
        $this->streamFactory->expects(self::once())
            ->method('createFromString')
            ->with('test')
            ->willReturn($stream = $this->createMock(StreamInterface::class));

        $response = $this->factory->createResponseFromString('test');

        self::assertSame($stream, $response->getBody());
        self::assertSame(200, $response->getStatusCode());
        self::assertSame([], $response->getHeaders());
    }

    public function testCreateResponseWillReturnInstanceOfResponseWithGivenStatusCode(): void
    {
        $statusCode = 201;

        self::assertSame($statusCode, $this->factory->createResponse($statusCode)->getStatusCode());
    }

    public function testCreatesApiProblemResponse(): void
    {
        $response = $this->factory->createJsonApiProblem(
            'Short Title',
            'Long Description'
        );

        self::assertSame(
            [
                'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
                'title' => 'Short Title',
                'detail' => 'Long Description',
                'status' => 500,
            ],
            \json_decode($response->getBody()->getContents(), true)
        );
        self::assertEquals(500, $response->getStatusCode());
    }

    public function testCreatesApiProblemResponseWithAdditionalInformation(): void
    {
        $response = $this->factory->createJsonApiProblem(
            'Short Title',
            'Long Description',
            456,
            ['foo' => 'bar'],
            'https://connect.mos.com/error/oops'
        );

        self::assertEquals(
            [
                'type' => 'https://connect.mos.com/error/oops',
                'title' => 'Short Title',
                'detail' => 'Long Description',
                'status' => 456,
                'foo' => 'bar',
            ],
            \json_decode($response->getBody()->getContents(), true)
        );
        self::assertSame(456, $response->getStatusCode());
    }
}
