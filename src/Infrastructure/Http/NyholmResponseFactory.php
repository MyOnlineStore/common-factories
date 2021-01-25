<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Http\ResponseFactory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

final class NyholmResponseFactory implements ResponseFactory
{
    private const RFC2616 = 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html';

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
            $data = \array_merge($data, $additionalInformation);
        }

        return $this->createJsonResponse($data, $statusCode);
    }

    /**
     * @inheritDoc
     */
    public function createJsonResponse(
        $data,
        int $statusCode = 200,
        array $headers = [],
        int $encodingOptions = \JSON_UNESCAPED_UNICODE
    ): ResponseInterface {
        $json = \json_encode($data, $encodingOptions);

        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException(
                \sprintf('Unable to encode data to JSON in %s: %s', self::class, \json_last_error_msg())
            );
        }

        return new Response(
            $statusCode,
            \array_merge(
                ['content-type' => 'application/json'],
                $headers
            ),
            $json
        );
    }

    /**
     * @inheritDoc
     */
    public function createRedirectResponse($uri, int $statusCode = 302): ResponseInterface
    {
        return new Response($statusCode, ['location' => (string) $uri]);
    }

    public function createResponse(int $statusCode = 200): ResponseInterface
    {
        return new Response($statusCode);
    }

    /**
     * @inheritDoc
     */
    public function createResponseFromString(
        string $body,
        int $statusCode = 200,
        array $headers = []
    ): ResponseInterface {
        return new Response($statusCode, $headers, $body);
    }
}
