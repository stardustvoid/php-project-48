<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private $resultStylish;
    private $resultPlain;
    private $resultJson;

    public function getFixtureFullPath($fixtureName)
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function setUp(): void
    {
        $this->resultStylish = $this->getFixtureFullPath('result-stylish.txt');
        $this->resultPlain = $this->getFixtureFullPath('result-plain.txt');
        $this->resultJson = $this->getFixtureFullPath('result-json.txt');
    }

    public function testJson(): void
    {
        $json1 = $this->getFixtureFullPath('file1.json');
        $json2 = $this->getFixtureFullPath('file2.json');

        $diffStylish = genDiff($json1, $json2);
        $diffPlain = genDiff($json1, $json2, 'plain');
        $diffJson = genDiff($json1, $json2, 'json');

        $this->assertStringEqualsFile($this->resultStylish, $diffStylish);
        $this->assertStringEqualsFile($this->resultPlain, $diffPlain);
        $this->assertStringEqualsFile($this->resultJson, $diffJson);
    }

    public function testYaml(): void
    {
        $yaml1 = $this->getFixtureFullPath('file1.yaml');
        $yaml2 = $this->getFixtureFullPath('file2.yaml');

        $diffStylish = genDiff($yaml1, $yaml2);
        $diffPlain = genDiff($yaml1, $yaml2, 'plain');
        $diffJson = genDiff($yaml1, $yaml2, 'json');

        $this->assertStringEqualsFile($this->resultStylish, $diffStylish);
        $this->assertStringEqualsFile($this->resultPlain, $diffPlain);
        $this->assertStringEqualsFile($this->resultJson, $diffJson);
    }
}
