<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\PersistingStoreInterface;

interface SymfonyLockFactoryProvider
{
    /** @param int $timeout Number of milliseconds to wait for lock */
    public function getFactory(PersistingStoreInterface $store, int $timeout = 0): LockFactory;
}
