<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class SymfonyResponseFactory
{
    private const RFC2616 = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';

    /**
     * Creates a RFC7807 API Problem response
     *
     * @link https://tools.ietf.org/html/rfc7807
     *
     * @param mixed[] $additionalInformation
     */
    public function createJsonApiProblem(
        string $title,
        string $detail,
        int $statusCode = 500,
        ?array $additionalInformation = null,
        ?string $type = null
    ): JsonResponse {
        $data = [
            'type' => $type ?: self::RFC2616,
            'title' => $title,
            'detail' => $detail,
            'status' => $statusCode,
        ];

        if (null !== $additionalInformation) {
            $data = \array_merge($data, $additionalInformation);
        }

        return $this->createJsonResponse($data, $statusCode);
    }

    /**
     * @param mixed    $data
     * @param string[] $headers
     */
    public function createJsonResponse($data, int $statusCode = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $statusCode, $headers);
    }

    public function createRedirectResponse(string $uri, int $statusCode = 302): RedirectResponse
    {
        return new RedirectResponse($uri, $statusCode);
    }

    public function createResponse(int $statusCode = 200): Response
    {
        return new Response(null, $statusCode);
    }

    /**
     * @param string[] $headers
     */
    public function createResponseFromString(
        string $body,
        int $statusCode = 200,
        array $headers = []
    ): Response {
        return new Response($body, $statusCode, $headers);
    }
}
