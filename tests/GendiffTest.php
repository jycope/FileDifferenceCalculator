<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;
use Differ\differenceFiles\genDiff;

use function Differ\differenceFiles\genDiff;

class GendiffTest extends TestCase
{
    public function testDifferenceFilesRelativePath(): void
    {
        $filePath1 = 'tests/fixtures/file1.json';
        $filePath2 = 'tests/fixtures/file2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/expectedWhichPathTest.txt');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testDifferenceFilesAbsolutePath(): void
    {
        $filePath1 = __DIR__ . '/fixtures/file1.json';
        $filePath2 = __DIR__ . '/fixtures/file2.json';
        
        $expected = file_get_contents(__DIR__ . '/fixtures/expectedWhichPathTest.txt');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testEmptyFiles()
    {
        $filePath1 = __DIR__ . '/fixtures/fileEmpty1.json';
        $filePath2 = __DIR__ . '/fixtures/fileEmpty2.json';

        $expected = file_get_contents(__DIR__ . '/fixtures/expectedWhichEmptyFiles.json');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }
}
