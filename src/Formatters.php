<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;
use function Differ\Formatters\Plain\formatPlain;

function format(array $diff, string $formatter): string
{
    return match ($formatter) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        default => throw new \Exception('Wrong format name')
    };
}
