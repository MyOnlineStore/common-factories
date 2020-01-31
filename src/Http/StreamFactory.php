<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Http;

use Psr\Http\Message\StreamInterface;

interface StreamFactory
{
    public function createFromString(string $body): StreamInterface;
}
