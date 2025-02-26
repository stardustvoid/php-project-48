<?php

namespace Differ\Formatters\Plain;

function toStringWithQuotes($value): string
{
    if (is_null($value)) {
        return 'null';
    }

    return var_export($value, true);
}

function formatValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    return toStringWithQuotes($value);
}

function buildPlainStrings(array $diff, array $ancestry = []): array
{
    return array_reduce($diff, function ($acc, $node) use ($ancestry) {
        $status = $node['type'];
        $newAncestry = [...$ancestry, $node['key']];
        $nodeFullName = implode('.', $newAncestry);
        $value = formatValue($node['value'] ?? null);
        $newValue = formatValue($node['newValue'] ?? null);
        $oldValue = formatValue($node['oldValue'] ?? null);
        $children = $node['children'] ?? null;


        $line = match ($status) {
            'nested' => buildPlainStrings($children, $newAncestry),
            'added' => ["Property '$nodeFullName' was $status with value: $value"],
            'removed' => ["Property '$nodeFullName' was $status"],
            'updated' => ["Property '$nodeFullName' was $status. From $oldValue to $newValue"],
            default => []
        };

        return [...$acc, ...$line];
    }, []);
}

function formatPlain(array $diff): string
{
    $lines = buildPlainStrings($diff);

    return implode("\n", $lines);
}
