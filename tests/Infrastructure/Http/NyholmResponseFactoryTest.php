<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Infrastructure\Http\NyholmResponseFactory;
use PHPUnit\Framework\TestCase;

final class NyholmResponseFactoryTest extends TestCase
{
    /** @var NyholmResponseFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new NyholmResponseFactory();
    }

    public function testCreateJsonResponseWillReturnInstanceOfResponse(): void
    {
        $data = ['foo' => 'Twoich wyborów'];
        $headers = ['qux' => 'lax'];

        $response = $this->factory->createJsonResponse($data, 203, $headers, \JSON_PARTIAL_OUTPUT_ON_ERROR);

        self::assertSame($data, \json_decode((string) $response->getBody(), true));
        self::assertSame(203, $response->getStatusCode());
        self::assertEquals(['qux' => ['lax'], 'content-type' => ['application/json']], $response->getHeaders());
    }

    public function testCreateJsonResponseThrowsExceptionIfEncodingFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createJsonResponse(\fopen('php://temp', 'r'));
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
        $response = $this->factory->createResponseFromString('test', 204, ['foo' => 'bar']);

        self::assertSame('test', (string) $response->getBody());
        self::assertSame(204, $response->getStatusCode());
        self::assertSame(['foo' => ['bar']], $response->getHeaders());
    }

    public function testCreateResponseFromStringWithBodyAndStatusCodeOnlyWillReturnInstanceOfResponse(): void
    {
        $response = $this->factory->createResponseFromString('test', 204);

        self::assertSame('test', (string) $response->getBody());
        self::assertEquals(204, $response->getStatusCode());
        self::assertEquals([], $response->getHeaders());
    }

    public function testCreateResponseFromStringWithBodyOnlyWillReturnInstanceOfResponse(): void
    {
        $response = $this->factory->createResponseFromString('test');

        self::assertSame('test', (string) $response->getBody());
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
            \json_decode((string) $response->getBody(), true)
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
            \json_decode((string) $response->getBody(), true)
        );
        self::assertSame(456, $response->getStatusCode());
    }
}
