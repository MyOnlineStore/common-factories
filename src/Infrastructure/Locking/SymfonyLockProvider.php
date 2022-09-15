<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use MyOnlineStore\Common\Factory\Locking\LockNotAcquirable;
use MyOnlineStore\Common\Factory\Locking\LockProvider;
use Symfony\Component\Lock\Exception\LockAcquiringException;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\PersistingStoreInterface;

final class SymfonyLockProvider implements LockProvider
{
    /** @var array<string, LockInterface> */
    private array $locks = [];

    public function __construct(
        private SymfonyLockFactoryProvider $lockFactoryProvider,
        private PersistingStoreInterface $storage,
    ) {
    }

    public function acquire(string $name, int $timeout = 10): void
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
        } catch (LockConflictedException | LockAcquiringException $exception) {
        }

        throw new LockNotAcquirable(
            \sprintf('Unable to acquire lock "%s"', $name),
            0,
            isset($exception) && $exception instanceof \Throwable ? $exception : null,
        );
    }

    /** @inheritDoc */
    public function getAcquiredLockNames(): array
    {
        return \array_keys($this->locks);
    }

    public function release(string $name): void
    {
        if (!isset($this->locks[$name])) {
            return;
        }

        $this->locks[$name]->release();
        unset($this->locks[$name]);
    }
}
