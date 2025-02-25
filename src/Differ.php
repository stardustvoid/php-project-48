<?php

namespace Differ\Differ;

use function Differ\Parser\parse;
use function Differ\Formatters\format;

function getKeys(array $oldData, array $newData): array
{
    $uniqueKeys = array_unique(array_merge(array_keys($oldData), array_keys($newData)));

    sort($uniqueKeys);

    return $uniqueKeys;
}

function normalizeValue(mixed $value): mixed
{
    if (is_object($value)) {
        return json_decode(json_encode($value), associative: true);
    }

    return $value;
}

function getNodeType(string $key, array $oldData, array $newData): string
{
    if (!array_key_exists($key, $oldData)) {
        return 'added';
    } elseif (!array_key_exists($key, $newData)) {
        return 'removed';
    }

    $oldValue = $oldData[$key];
    $newValue = $newData[$key];

    if (is_object($oldValue) && is_object($newValue)) {
        return 'nested';
    } elseif ($oldValue !== $newValue) {
        return 'updated';
    }

    return 'unchanged';
}

function buildDiffNode(string $key, array $oldData, array $newData): array
{
    $oldValue = $oldData[$key] ?? null;
    $newValue = $newData[$key] ?? null;
    $normalizedOldValue = normalizeValue($oldValue);
    $normalizedNewValue = normalizeValue($newValue);

    $nodeType = getNodeType($key, $oldData, $newData);

    $node = [
        'type' => $nodeType,
        'key' => $key
    ];

    $nodeValue = match ($nodeType) {
        'added' => [
            'value' => $normalizedNewValue
        ],
        'unchanged', 'removed' => [
            'value' => $normalizedOldValue
        ],
        'nested' => [
            'children' => buildDiff($oldValue, $newValue)
        ],
        'updated' => [
            'oldValue' => $normalizedOldValue,
            'newValue' => $normalizedNewValue
        ],
        default => throw new \Exception('Wrong node type')
    };

    return [...$node, ...$nodeValue];
}

function buildDiff(object $oldDataObj, object $newDataObj): array
{
    $oldData = (array) $oldDataObj;
    $newData = (array) $newDataObj;

    $keys = getKeys($oldData, $newData);

    return array_map(fn($key) => buildDiffNode($key, $oldData, $newData), $keys);
}

function genDiff(string $pathToFile1, string $pathToFile2, string $format = 'stylish'): string
{
    $oldDataObj = parse($pathToFile1);
    $newDataObj = parse($pathToFile2);

    $diff = buildDiff($oldDataObj, $newDataObj);

    return format($diff, $format);
}
