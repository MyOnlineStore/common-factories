<?php
declare(strict_types=1);

namespace MyOnlineStore\Common\Factory\Random;

interface TokenGenerator
{
    public const DEFAULT_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generate(int $size): string;

    public function generateInt(int $min, int $max = \PHP_INT_MAX): int;

    public function generateString(int $length, string $characters = self::DEFAULT_CHARACTERS): string;
}
