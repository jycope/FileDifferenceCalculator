<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;
use Differ\DifferenceFiles\genDiff;

use function Differ\DifferenceFiles\genDiff;
use function Differ\DifferenceFiles\convertedToJson;

class GendiffTest extends TestCase
{
    public function testDifferenceJsonFilesRelativePath(): void
    {
        $filePath1 = __DIR__ . '/fixtures/json/file1.json';
        $filePath2 = __DIR__ . '/fixtures/json/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/expectedWhichPathTest.txt');

        $this->assertEquals($expected, convertedToJson(genDiff($filePath1, $filePath2)));
    }

    public function testDifferenceJsonFilesAbsolutePath(): void
    {
        $filePath1 = 'tests/fixtures/json/file1.json';
        $filePath2 = 'tests/fixtures/json/file2.json';

        $expected = file_get_contents('tests/fixtures/expectedWhichPathTest.txt');

        $this->assertEquals($expected, convertedToJson(genDiff($filePath1, $filePath2)));
    }

    public function testEmptyJsonFiles(): void
    {
        $filePath1 = 'tests/fixtures/json/fileEmpty1.json';
        $filePath2 = 'tests/fixtures/json/fileEmpty2.json';

        $expected = file_get_contents('tests/fixtures/json/expectedWhichEmptyFiles.json');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testDifferenceYamlFilesRelativePath(): void
    {
        $filePath1 = __DIR__ . '/fixtures/yaml/file1.yaml';
        $filePath2 = __DIR__ . '/fixtures/yaml/file2.yaml';

        $expected = file_get_contents(__DIR__ . '/fixtures/expectedWhichPathTest.txt');

        $this->assertEquals($expected, convertedToJson(genDiff($filePath1, $filePath2)));
    }

    public function testDifferenceYamlFilesAbsolutePath(): void
    {
        $filePath1 = 'tests/fixtures/yaml/fileEmpty1.yaml';
        $filePath2 = 'tests/fixtures/yaml/fileEmpty2.yaml';

        $expected = file_get_contents('tests/fixtures/json/expectedWhichEmptyFiles.json');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testDifferenceNestedFiles(): void
    {
        $filePath1 = 'tests/fixtures/json/fileNested1.json';
        $filePath2 = 'tests/fixtures/json/fileNested2.json';

        $expected = file_get_contents('tests/fixtures/expectedWhichNestedFilesTest.txt');

        $this->assertEquals($expected, convertedToJson(genDiff($filePath1, $filePath2)));
    }

    public function testPlainFormat(): void
    {
        $filePath1 = 'tests/fixtures/json/fileNested1.json';
        $filePath2 = 'tests/fixtures/json/fileNested2.json';

        $expected = file_get_contents('tests/fixtures/expectedWhichNestedFilesTest.txt');

        $this->assertEquals($expected, convertedToJson(genDiff($filePath1, $filePath2, 'plain')));
    }
}
