<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Locking;

use MyOnlineStore\Common\Factory\Infrastructure\Locking\SymfonyLockFactoryProvider;
use MyOnlineStore\Common\Factory\Infrastructure\Locking\SymfonyLockProvider;
use MyOnlineStore\Common\Factory\Locking\LockNotAcquirable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\PersistingStoreInterface;

final class SymfonyLockProviderTest extends TestCase
{
    /** @var LockInterface&MockObject */
    private $lock;

    /** @var LockFactory&MockObject */
    private $lockFactory;

    /** @var SymfonyLockFactoryProvider&MockObject */
    private $lockFactoryProvider;

    /** @var SymfonyLockProvider */
    private $lockProvider;

    /** @var PersistingStoreInterface&MockObject */
    private $storage;

    protected function setUp(): void
    {
        $this->lockProvider = new SymfonyLockProvider(
            $this->lockFactoryProvider = $this->createMock(SymfonyLockFactoryProvider::class),
            $this->storage = $this->createMock(PersistingStoreInterface::class)
        );

        $this->lock = $this->createMock(LockInterface::class);
        $this->lockFactory = $this->createMock(LockFactory::class);
    }

    /**
     * @return \Generator<array{int, bool}>
     */
    public function blockingTimoutDataProvider(): \Generator
    {
        yield [0, false];
        yield [1, true];
        yield [-1, false];
    }

    public function testGetAcquiredLockNames(): void
    {
        $lockName = 'foo';

        $property = new \ReflectionProperty(SymfonyLockProvider::class, 'locks');
        $property->setAccessible(true);
        $property->setValue($this->lockProvider, [$lockName => $this->createMock(LockInterface::class)]);

        self::assertEquals([$lockName], $this->lockProvider->getAcquiredLockNames());
    }

    /**
     * @dataProvider blockingTimoutDataProvider
     */
    public function testGetWillAcquireLock(int $timeout, bool $blocking): void
    {
        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 1000 * $timeout)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())->method('acquire')->with($blocking)->willReturn(true);

        $this->lockProvider->acquire('foo', $timeout);
    }

    public function testGetWillThrowExceptionIfLockAcquireFailsWithAnException(): void
    {
        $this->expectException(LockNotAcquirable::class);

        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 10000)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())
            ->method('acquire')
            ->with(true)
            ->willThrowException(new LockAcquiringException());

        $this->lockProvider->acquire('foo');
    }

    public function testGetWillThrowExceptionIfLockCannotBeAcquired(): void
    {
        $this->expectException(LockNotAcquirable::class);

        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 10000)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())->method('acquire')->with(true)->willReturn(false);

        $this->lockProvider->acquire('foo');
    }

    public function testGetWillThrowExceptionIfLockHasBeenAcquiredInSameRuntime(): void
    {
        $this->expectException(LockNotAcquirable::class);

        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 0)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())->method('acquire')->with(false)->willReturn(true);

        $this->lockProvider->acquire('foo', 0);
        $this->lockProvider->acquire('foo', 0);
    }

    public function testGetWillThrowExceptionIfLockIsConflicted(): void
    {
        $this->expectException(LockNotAcquirable::class);

        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 10000)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())
            ->method('acquire')
            ->with(true)
            ->willThrowException(new LockConflictedException());

        $this->lockProvider->acquire('foo');
    }

    public function testReleaseWillDoNothingIfLockWasNotAcquired(): void
    {
        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 0)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())->method('acquire')->with(false)->willReturn(true);
        $this->lock->expects(self::never())->method('release');

        $this->lockProvider->acquire('foo', 0);
        $this->lockProvider->release('bar');
    }

    public function testReleaseWillReleaseAcquiredLock(): void
    {
        $this->lockFactoryProvider->expects(self::once())
            ->method('getFactory')
            ->with($this->storage, 0)
            ->willReturn($this->lockFactory);

        $this->lockFactory->expects(self::once())
            ->method('createLock')
            ->with('foo')
            ->willReturn($this->lock);

        $this->lock->expects(self::once())->method('acquire')->with(false)->willReturn(true);
        $this->lock->expects(self::once())->method('release');

        $this->lockProvider->acquire('foo', 0);
        $this->lockProvider->release('foo');
    }
}
