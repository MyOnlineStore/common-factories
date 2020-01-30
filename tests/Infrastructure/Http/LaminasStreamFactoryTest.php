<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Http;

use MyOnlineStore\Common\Factory\Infrastructure\Http\LaminasStreamFactory;
use PHPUnit\Framework\TestCase;

final class LaminasStreamFactoryTest extends TestCase
{
    /** @var LaminasStreamFactory */
    private $factory;

    protected function setUp()
    {
        $this->factory = new LaminasStreamFactory();
    }

    public function testCreateFromStringWillReturnStream()
    {
        $stream = $this->factory->createFromString('test');

        self::assertSame('test', $stream->getContents());
    }
}
