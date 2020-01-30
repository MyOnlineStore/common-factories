<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\RetryTillSaveStore;
use Symfony\Component\Lock\StoreInterface;

final class SymfonyBlockingLockFactoryProvider implements SymfonyLockFactoryProvider
{
    const INTERVAL = 100;

    public function getFactory(StoreInterface $store, int $timeout = 0): Factory
    {
        if (0 < $timeout) {
            $store = new RetryTillSaveStore($store, self::INTERVAL, (int) \ceil($timeout / self::INTERVAL));
        }

        return new Factory($store);
    }
}
