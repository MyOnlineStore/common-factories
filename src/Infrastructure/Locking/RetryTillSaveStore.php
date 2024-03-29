<?php
declare(strict_types=1);

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MyOnlineStore\Common\Factory\Infrastructure\Locking;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Lock\BlockingStoreInterface;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\PersistingStoreInterface;

/**
 * RetryTillSaveStore is a PersistingStoreInterface implementation which decorate a non blocking
 * PersistingStoreInterface to provide a blocking storage.
 *
 * Ported from Symfony: https://github.com/symfony/symfony/issues/40684
 */
class RetryTillSaveStore implements BlockingStoreInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param positive-int $retrySleep Duration in ms between 2 retry
     * @param int          $retryCount Maximum amount of retry
     */
    public function __construct(
        private PersistingStoreInterface $decorated,
        private int $retrySleep = 100,
        private int $retryCount = \PHP_INT_MAX,
    ) {
        $this->logger = new NullLogger();
    }

    public function save(Key $key): void
    {
        $this->decorated->save($key);
    }

    public function waitAndSave(Key $key): void
    {
        $retry = 0;
        $sleepRandomness = (int) ($this->retrySleep / 10);
        do {
            try {
                $this->decorated->save($key);

                return;
            } catch (LockConflictedException) {
                /** @psalm-suppress ArgumentTypeCoercion */
                \usleep(($this->retrySleep + \random_int(-$sleepRandomness, $sleepRandomness)) * 1000);
            }
        } while (++$retry < $this->retryCount);

        /** @psalm-suppress PossiblyNullReference */
        $this->logger->warning(
            'Failed to store the "{resource}" lock. Abort after {retry} retry.',
            ['resource' => $key, 'retry' => $retry],
        );

        throw new LockConflictedException();
    }

    public function putOffExpiration(Key $key, float $ttl): void
    {
        $this->decorated->putOffExpiration($key, $ttl);
    }

    public function delete(Key $key): void
    {
        $this->decorated->delete($key);
    }

    public function exists(Key $key): bool
    {
        return $this->decorated->exists($key);
    }
}
