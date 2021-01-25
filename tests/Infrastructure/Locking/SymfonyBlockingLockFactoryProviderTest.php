<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Locking;

use MyOnlineStore\Common\Factory\Infrastructure\Locking\SymfonyBlockingLockFactoryProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

final class SymfonyBlockingLockFactoryProviderTest extends TestCase
{
    /** @var SymfonyBlockingLockFactoryProvider */
    private $lockFactoryProvider;

    protected function setUp(): void
    {
        $this->lockFactoryProvider = new SymfonyBlockingLockFactoryProvider();
    }

    public function testGetFactoryWillReturnFactoryInstance(): void
    {
        $factory = $this->lockFactoryProvider->getFactory(
            $store = $this->createMock(PersistingStoreInterface::class),
            0
        );

        self::assertSame($store, $this->getLock($factory));
    }

    public function testGetFactoryWithTimeoutWillReturnFactoryInstanceWithDecoratedStorage(): void
    {
        $factory = $this->lockFactoryProvider->getFactory(
            $this->createMock(PersistingStoreInterface::class),
            100
        );

        self::assertInstanceOf(RetryTillSaveStore::class, $this->getLock($factory));
    }

    private function getLock(LockFactory $factory): object
    {
        $reflector = new \ReflectionObject($factory);

        do {
            try {
                $attribute = $reflector->getProperty('store');

                $attribute->setAccessible(true);
                $value = $attribute->getValue($factory);
                \assert(\is_object($value));
                $attribute->setAccessible(false);

                return $value;
            } catch (\ReflectionException $e) {
            }
        } while ($reflector = $reflector->getParentClass());

        throw new \RuntimeException('No store found');
    }
}
