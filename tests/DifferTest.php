<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private $flatResult;
    // private $result;

    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp(): void
    {
        $this->flatResult = $this->getFixtureFullPath('flat_result.txt');
    }

    public function testFlatJson()
    {
        $json1 = $this->getFixtureFullPath('file1.json');
        $json2 = $this->getFixtureFullPath('file2.json');

        $diff = genDiff($json1, $json2);

        $this->assertStringEqualsFile($this->flatResult, $diff);
    }

    public function testFlatYaml()
    {
        $yaml1 = $this->getFixtureFullPath('file1.yaml');
        $yaml2 = $this->getFixtureFullPath('file2.yaml');

        $diff = genDiff($yaml1, $yaml2);

        $this->assertStringEqualsFile($this->flatResult, $diff);
    }
}
