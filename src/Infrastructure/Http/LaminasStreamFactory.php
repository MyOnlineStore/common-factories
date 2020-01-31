<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Http;

use Laminas\Diactoros\Stream;
use MyOnlineStore\Common\Factory\Http\StreamFactory;
use Psr\Http\Message\StreamInterface;

final class LaminasStreamFactory implements StreamFactory
{
    public function createFromString(string $body): StreamInterface
    {
        $stream = new Stream('php://temp', 'wb+');
        $stream->write($body);
        $stream->rewind();

        return $stream;
    }
}
