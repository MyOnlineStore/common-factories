<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ResponseFactory
{
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
    ): ResponseInterface;

    /**
     * @param mixed    $data
     * @param string[] $headers
     */
    public function createJsonResponse(
        $data,
        int $statusCode = 200,
        array $headers = [],
        int $encodingOptions = \JSON_UNESCAPED_UNICODE
    ): ResponseInterface;

    /**
     * @param string|UriInterface $uri
     */
    public function createRedirectResponse($uri, int $statusCode = 302): ResponseInterface;

    public function createResponse(int $statusCode = 200): ResponseInterface;

    /**
     * @param string[]|string[][] $headers
     */
    public function createResponseFromString(
        string $body,
        int $statusCode = 200,
        array $headers = []
    ): ResponseInterface;
}
