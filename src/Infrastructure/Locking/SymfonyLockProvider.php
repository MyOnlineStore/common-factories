<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use MyOnlineStore\Common\Factory\Locking\LockNotAcquirable;
use MyOnlineStore\Common\Factory\Locking\LockProvider;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\StoreInterface;

final class SymfonyLockProvider implements LockProvider
{
    /** @var SymfonyLockFactoryProvider */
    private $lockFactoryProvider;

    /**
     * @var LockInterface[]
     *
     * @psalm-var array<string, LockInterface>
     */
    private $locks = [];

    /** @var StoreInterface */
    private $storage;

    public function __construct(
        SymfonyLockFactoryProvider $lockFactoryProvider,
        StoreInterface $storage
    ) {
        $this->lockFactoryProvider = $lockFactoryProvider;
        $this->storage = $storage;
    }

    /**
     * @inheritDoc
     */
    public function acquire(string $name, int $timeout = 10)
    {
        if (isset($this->locks[$name])) {
            throw new LockNotAcquirable(\sprintf('Lock "%s" was already acquired', $name));
        }

        $lock = $this->lockFactoryProvider->getFactory($this->storage, $timeout * 1000)
            ->createLock($name);

        try {
            if ($lock->acquire(0 < $timeout)) {
                $this->locks[$name] = $lock;

                return;
            }
        } catch (LockConflictedException $exception) {
        } catch (LockAcquiringException $exception) {
        }

        throw new LockNotAcquirable(
            \sprintf('Unable to acquire lock "%s"', $name),
            0,
            isset($exception) && $exception instanceof \Throwable ? $exception : null
        );
    }

    /**
     * @inheritDoc
     */
    public function getAcquiredLockNames(): array
    {
        return \array_keys($this->locks);
    }

    public function release(string $name)
    {
        if (!isset($this->locks[$name])) {
            return;
        }

        $this->locks[$name]->release();
        unset($this->locks[$name]);
    }
}
