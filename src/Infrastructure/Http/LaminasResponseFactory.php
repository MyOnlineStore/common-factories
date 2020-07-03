<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Http;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use MyOnlineStore\Common\Factory\Http\ResponseFactory;
use MyOnlineStore\Common\Factory\Http\StreamFactory;
use Psr\Http\Message\ResponseInterface;

final class LaminasResponseFactory implements ResponseFactory
{
    private const RFC2616 = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';

    /** @var StreamFactory */
    private $streamFactory;

    public function __construct(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    /**
     * @inheritDoc
     */
    public function createJsonApiProblem(
        string $title,
        string $detail,
        int $statusCode = 500,
        ?array $additionalInformation = null,
        ?string $type = null
    ): ResponseInterface {
        $data = [
            'type' => $type ?: self::RFC2616,
            'title' => $title,
            'detail' => $detail,
            'status' => $statusCode,
        ];

        if (null !== $additionalInformation) {
            $data = \array_merge($additionalInformation, $data);
        }

        return new JsonResponse($data, $statusCode);
    }

    /**
     * @inheritDoc
     */
    public function createJsonResponse($data, int $statusCode = 200): ResponseInterface
    {
        return new JsonResponse($data, $statusCode);
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse($uri, int $statusCode = 302): ResponseInterface
    {
        return new RedirectResponse($uri, $statusCode);
    }

    public function createResponse(int $statusCode = 200): ResponseInterface
    {
        return new Response('php://memory', $statusCode);
    }

    /**
     * @inheritDoc
     */
    public function createResponseFromString(
        string $body,
        int $statusCode = 200,
        array $headers = []
    ): ResponseInterface {
        return new Response(
            $this->streamFactory->createFromString($body),
            $statusCode,
            $headers
        );
    }
}
