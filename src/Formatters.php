<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\formatStylish;

function formatPlain(array $diff): string
{
    return formatStylish($diff);
}

function format(array $diff, string $formatter): string
{
    return match ($formatter) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        default => throw new \Exception('Wrong format name')
    };
}
