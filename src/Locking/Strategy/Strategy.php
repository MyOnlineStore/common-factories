<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Locking\Strategy;

interface Strategy
{
    public const DEFAULT_DURATION = 10;

    public function getLockName(): string;

    /**
     * @return int Seconds
     */
    public function getLockDuration(): int;
}
