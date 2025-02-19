<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testFlatJson()
    {
        $json1 = $this->getFixtureFullPath('file1.json');
        $json2 = $this->getFixtureFullPath('file2.json');
        $result = $this->getFixtureFullPath('flat_result.txt');

        $diff = genDiff($json1, $json2);

        $this->assertStringEqualsFile($result, $diff);
    }
}
