<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Infrastructure\Http\SymfonyResponseFactory;
use PHPUnit\Framework\TestCase;

final class SymfonyResponseFactoryTest extends TestCase
{
    /** @var SymfonyResponseFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new SymfonyResponseFactory();
    }

    public function testCreateApiProblem(): void
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
            \json_decode((string) $response->getContent(), true)
        );
        self::assertEquals(500, $response->getStatusCode());
    }

    public function testCreateApiProblemWithAdditionalInformation(): void
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
            \json_decode((string) $response->getContent(), true)
        );
        self::assertSame(456, $response->getStatusCode());
    }

    public function testCreateJsonResponseThrowsExceptionIfEncodingFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createJsonResponse(\fopen('php://temp', 'r'));
    }

    public function testCreateJsonResponseWillReturnInstanceOfResponse(): void
    {
        $data = ['foo' => 'Twoich wyborów'];
        $headers = ['qux' => 'lax'];

        $response = $this->factory->createJsonResponse($data, 203, $headers);

        self::assertSame($data, \json_decode((string) $response->getContent(), true));
        self::assertSame(203, $response->getStatusCode());

        $expectedHeaders = ['qux' => ['lax'], 'content-type' => ['application/json']];
        self::assertEquals(
            $expectedHeaders,
            \array_intersect_key($response->headers->all(), $expectedHeaders)
        );
    }

    public function testCreateRedirectResponseWillReturnInstanceOfResponseWithGivenStatusCode(): void
    {
        $response = $this->factory->createRedirectResponse(
            $uri = 'http://foobar.baz',
            $statusCode = 303
        );

        self::assertSame($statusCode, $response->getStatusCode());
        self::assertSame($uri, $response->headers->get('location'));
    }

    public function testCreateResponseFromStringWillReturnInstanceOfResponse(): void
    {
        $response = $this->factory->createResponseFromString('test', 204, ['foo' => 'bar']);

        self::assertSame('test', (string) $response->getContent());
        self::assertSame(204, $response->getStatusCode());

        $expectedHeaders = ['foo' => ['bar']];
        self::assertSame(
            $expectedHeaders,
            \array_intersect_key($response->headers->allPreserveCase(), $expectedHeaders)
        );
    }

    public function testCreateResponseFromStringWithBodyAndStatusCodeOnlyWillReturnInstanceOfResponse(): void
    {
        $response = $this->factory->createResponseFromString('test', 204);

        self::assertSame('test', (string) $response->getContent());
        self::assertEquals(204, $response->getStatusCode());
    }

    public function testCreateResponseWillReturnInstanceOfResponseWithGivenStatusCode(): void
    {
        $statusCode = 201;

        self::assertSame($statusCode, $this->factory->createResponse($statusCode)->getStatusCode());
    }
}
