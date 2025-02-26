<?php

namespace Differ\Formatters\Stylish;

function toString($value): string
{
    if (is_null($value)) {
        return 'null';
    }

    return trim(var_export($value, true), "'");
}

function makeIndent(int $depth, string $indentType = 'line'): string
{
    $char = ' ';
    $size = 4;
    $leftShift = 2;

    $indentSize = match ($indentType) {
        'line' => $depth * $size - $leftShift,
        'nested' => $depth * $size,
        'bracket' => $depth * $size - $size,
        default => throw new \Exception('Wrong indent type')
    };

    return str_repeat($char, $indentSize);
}

function formatValue(mixed $value, int $depth): string
{
    if (!is_array($value)) {
        return toString($value);
    }

    $indent = makeIndent($depth, 'nested');
    $bracketIndent = makeIndent($depth, 'bracket');

    $lines = array_map(
        function ($key, $currentVal) use ($depth, $indent) {
            $formattedVal = formatValue($currentVal, $depth + 1);

            return "{$indent}{$key}: {$formattedVal}";
        },
        array_keys($value),
        $value
    );

    $result = ['{', ...$lines, "{$bracketIndent}}"];

    return implode("\n", $result);
}

function buildString(array $node, int $depth): array
{
    $type = $node['type'];
    $key = $node['key'];

    $formattedValue = match ($type) {
        'nested' => formatStylish($node['children'], $depth + 1),
        'unchanged', 'added', 'removed' => formatValue($node['value'], $depth + 1),
        'updated' => [
            'old' => formatValue($node['oldValue'], $depth + 1),
            'new' => formatValue($node['newValue'], $depth + 1)
        ],
        default => throw new \Exception('Wrong node type')
    };

    $indent = makeIndent($depth);

    $line = match ($type) {
        'nested', 'unchanged' => [
            "{$indent}  {$key}: {$formattedValue}"
        ],
        'added' => [
            "{$indent}+ {$key}: {$formattedValue}"
        ],
        'removed' => [
            "{$indent}- {$key}: {$formattedValue}"
        ],
        'updated' => [
            "{$indent}- {$key}: {$formattedValue['old']}",
            "{$indent}+ {$key}: {$formattedValue['new']}"
        ],
        default => throw new \Exception('Wrong node type')
    };

    return array_map(fn($ln) => rtrim($ln), $line);
}

function formatStylish(array $diff, int $depth = 1): string
{
    $lines = array_reduce($diff, function ($acc, $node) use ($depth) {
        $line = buildString($node, $depth);
        return [...$acc, ...$line];
    }, []);

    $bracketIndent = makeIndent($depth, 'bracket');

    $result = ['{', ...$lines, "{$bracketIndent}}"];

    return implode("\n", $result);
}
