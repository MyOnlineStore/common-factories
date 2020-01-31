<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\StoreInterface;

interface SymfonyLockFactoryProvider
{
    /**
     * @param int $timeout Number of milliseconds to wait for lock
     */
    public function getFactory(StoreInterface $store, int $timeout = 0): Factory;
}
