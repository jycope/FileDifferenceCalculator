<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;
use Differ\differenceFiles\genDiff;

use function Differ\differenceFiles\genDiff;

class GendiffTest extends TestCase
{
    public function testDifferenceJsonFilesRelativePath(): void
    {
        $filePath1 = 'tests/fixtures/json/file1.json';
        $filePath2 = 'tests/fixtures/json/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/json/expectedWhichPathTest.txt');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testDifferenceJsonFilesAbsolutePath(): void
    {
        $filePath1 = __DIR__ . '/fixtures/json/file1.json';
        $filePath2 = __DIR__ . '/fixtures/json/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/json/expectedWhichPathTest.txt');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testEmptyJsonFiles()
    {
        $filePath1 = __DIR__ . '/fixtures/json/fileEmpty1.json';
        $filePath2 = __DIR__ . '/fixtures/json/fileEmpty2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/json/expectedWhichEmptyFiles.json');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }
}
