<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;

final class SymfonyBlockingLockFactoryProvider implements SymfonyLockFactoryProvider
{
    private const INTERVAL = 100;

    public function getFactory(PersistingStoreInterface $store, int $timeout = 0): LockFactory
    {
        if (0 < $timeout) {
            $store = new RetryTillSaveStore($store, self::INTERVAL, (int) \ceil($timeout / self::INTERVAL));
        }

        return new LockFactory($store);
    }
}
