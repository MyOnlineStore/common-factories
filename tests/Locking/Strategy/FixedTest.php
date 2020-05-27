<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Locking\Strategy;

use MyOnlineStore\Common\Factory\Locking\Strategy\Fixed;
use MyOnlineStore\Common\Factory\Locking\Strategy\Strategy;
use PHPUnit\Framework\TestCase;

final class FixedTest extends TestCase
{
    public function testGetters(): void
    {
        $strategy = new Fixed(
            $lockName = 'foo',
            $lockDuration = 132
        );

        self::assertSame($lockName, $strategy->getLockName());
        self::assertSame($lockDuration, $strategy->getLockDuration());
    }

    public function testDefaultLockDuration(): void
    {
        self::assertSame(Strategy::DEFAULT_DURATION, (new Fixed('foo'))->getLockDuration());
    }
}
