<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Locking;

use MyOnlineStore\Common\Factory\Infrastructure\Locking\SymfonyBlockingLockFactoryProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Store\RetryTillSaveStore;
use Symfony\Component\Lock\StoreInterface;

final class SymfonyBlockingLockFactoryProviderTest extends TestCase
{
    /** @var SymfonyBlockingLockFactoryProvider */
    private $lockFactoryProvider;

    protected function setUp()
    {
        $this->lockFactoryProvider = new SymfonyBlockingLockFactoryProvider();
    }

    public function testGetFactoryWillReturnFactoryInstance()
    {
        $factory = $this->lockFactoryProvider->getFactory(
            $store = $this->createMock(StoreInterface::class),
            0
        );

        self::assertAttributeSame($store, 'store', $factory);
    }

    public function testGetFactoryWithTimeoutWillReturnFactoryInstanceWithDecoratedStorage()
    {
        $factory = $this->lockFactoryProvider->getFactory(
            $this->createMock(StoreInterface::class),
            100
        );

        self::assertAttributeInstanceOf(RetryTillSaveStore::class, 'store', $factory);
    }
}
