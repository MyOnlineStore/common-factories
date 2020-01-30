<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Locking\Strategy;

final class Fixed implements Strategy
{
    /** @var string */
    private $lockName;

    /** @var int */
    private $duration;

    public function __construct(string $name, int $duration = self::DEFAULT_DURATION)
    {
        $this->lockName = $name;
        $this->duration = $duration;
    }

    public function getLockDuration(): int
    {
        return $this->duration;
    }

    public function getLockName(): string
    {
        return $this->lockName;
    }
}
