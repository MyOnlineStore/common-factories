<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\DateTime;

interface DateTimeFactory
{
    /**
     * @deprecated Use createImmutable()
     */
    public function create(string $time = 'now', ?\DateTimeZone $timezone = null): \DateTime;

    public function createImmutable(string $time = 'now', ?\DateTimeZone $timezone = null): \DateTimeImmutable;

    public function createImmutableFromFormat(string $format, string $time): \DateTimeImmutable;

    public function createImmutableFromMutable(\DateTime $dateTime): \DateTimeImmutable;
}
