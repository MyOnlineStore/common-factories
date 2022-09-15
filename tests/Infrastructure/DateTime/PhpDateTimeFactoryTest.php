<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\DateTime;

use MyOnlineStore\Common\Factory\Infrastructure\DateTime\PhpDateTimeFactory;
use PHPUnit\Framework\TestCase;

final class PhpDateTimeFactoryTest extends TestCase
{
    /** @var PhpDateTimeFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = new PhpDateTimeFactory();
    }

    public function testCreate(): void
    {
        $time = '2011-11-11';
        $dateTimeZone = new \DateTimeZone('Europe/Amsterdam');

        self::assertEquals(
            new \DateTime($time, $dateTimeZone),
            $this->factory->create($time, $dateTimeZone),
        );
    }

    public function testCreateImmutable(): void
    {
        $time = '2011-11-11';
        $dateTimeZone = new \DateTimeZone('Europe/Amsterdam');

        self::assertEquals(
            new \DateTimeImmutable($time, $dateTimeZone),
            $this->factory->createImmutable($time, $dateTimeZone),
        );
    }

    public function testCreateImmutableFromFormat(): void
    {
        self::assertEquals(
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-11-06 14:00:00'),
            $this->factory->createImmutableFromFormat('Y-m-d H:i:s', '2018-11-06 14:00:00'),
        );
    }

    public function testCreateImmutableFromMutable(): void
    {
        $dateTime = new \DateTime('2011-11-11', new \DateTimeZone('Europe/Amsterdam'));

        self::assertEquals(
            \DateTimeImmutable::createFromMutable($dateTime),
            $this->factory->createImmutableFromMutable($dateTime),
        );
    }
}
