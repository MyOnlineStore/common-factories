<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Tests\Infrastructure\Random;

use MyOnlineStore\Common\Factory\Infrastructure\Random\RandomLibTokenGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RandomLib\Generator;

final class RandomLibTokenGeneratorTest extends TestCase
{
    /** @var RandomLibTokenGenerator */
    private $generator;

    /** @var Generator&MockObject */
    private $mockGenerator;

    protected function setUp(): void
    {
        $this->generator = new RandomLibTokenGenerator(
            $this->mockGenerator = $this->createMock(Generator::class)
        );
    }

    public function testGenerate(): void
    {
        $this->mockGenerator->expects(self::once())
            ->method('generate')
            ->with(5)
            ->willReturn('foobar');

        self::assertSame('foobar', $this->generator->generate(5));
    }

    public function testGenerateInt(): void
    {
        $this->mockGenerator->expects(self::once())
            ->method('generateInt')
            ->with(5, 10)
            ->willReturn(7);

        self::assertSame(7, $this->generator->generateInt(5, 10));
    }

    public function testGenerateString(): void
    {
        $this->mockGenerator->expects(self::once())
            ->method('generateString')
            ->with(6, 'abc')
            ->willReturn('abcabc');

        self::assertSame('abcabc', $this->generator->generateString(6, 'abc'));
    }
}
