<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Infrastructure\Random;

use MyOnlineStore\Common\Factory\Random\TokenGenerator;
use RandomLib\Generator;

final class RandomLibTokenGenerator implements TokenGenerator
{
    /** @var Generator */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function generate(int $size): string
    {
        return $this->generator->generate($size);
    }

    public function generateInt(int $min, int $max = \PHP_INT_MAX): int
    {
        return $this->generator->generateInt($min, $max);
    }

    public function generateString(int $length, string $characters = self::DEFAULT_CHARACTERS): string
    {
        return $this->generator->generateString($length, $characters);
    }
}
