<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ResponseFactory
{
    /**
     * @param mixed $data
     */
    public function createJsonResponse($data, int $statusCode = 200): ResponseInterface;

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
