<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\DateTime;

use MyOnlineStore\Common\Factory\DateTime\DateTimeFactory;

final class PhpDateTimeFactory implements DateTimeFactory
{
    public function create(string $time = 'now', \DateTimeZone $timezone = null): \DateTime
    {
        return new \DateTime($time, $timezone);
    }

    public function createImmutable(string $time = 'now', \DateTimeZone $timezone = null): \DateTimeImmutable
    {
        return new \DateTimeImmutable($time, $timezone);
    }

    public function createImmutableFromFormat(string $format, string $time): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat($format, $time);
    }

    public function createImmutableFromMutable(\DateTime $dateTime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($dateTime);
    }
}
