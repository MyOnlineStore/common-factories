<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Http\StreamFactory;
use MyOnlineStore\Common\Factory\Infrastructure\Http\LaminasResponseFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

final class LaminasResponseFactoryTest extends TestCase
{
    /** @var StreamFactory */
    private $streamFactory;

    /** @var LaminasResponseFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new LaminasResponseFactory(
            $this->streamFactory = $this->createMock(StreamFactory::class)
        );
    }

    public function testCreateResponseWillReturnInstanceOfResponseWithGivenStatusCode()
    {
        $statusCode = 201;

        self::assertSame($statusCode, $this->factory->createResponse($statusCode)->getStatusCode());
    }

    public function testCreateResponseFromStringWithBodyOnlyWillReturnInstanceOfResponse()
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

    public function testCreateResponseFromStringWithBodyAndStatusCodeOnlyWillReturnInstanceOfResponse()
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

    public function testCreateResponseFromStringWillReturnInstanceOfResponse()
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

    public function testCreateJsonResponseWillReturnInstanceOfResponse()
    {
        $data = ['foo' => 'bar'];
        $response = $this->factory->createJsonResponse($data, 203);

        self::assertSame($data, \json_decode($response->getBody()->getContents(), true));
        self::assertSame(203, $response->getStatusCode());
    }

    public function testCreateRedirectResponseWillReturnInstanceOfResponseWithGivenStatusCode()
    {
        $response = $this->factory->createRedirectResponse(
            $uri = 'http://foobar.baz',
            $statusCode = 303
        );

        self::assertSame($statusCode, $response->getStatusCode());
        self::assertSame([$uri], $response->getHeader('location'));
    }
}
