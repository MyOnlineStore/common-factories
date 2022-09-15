<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Locking;

interface LockProvider
{
    /** @throws LockNotAcquirable */
    public function acquire(string $name, int $timeout = 10): void;

    /**
     * Returns the names of all acquired locks within this runtime
     *
     * @return string[]
     */
    public function getAcquiredLockNames(): array;

    public function release(string $name): void;
}
