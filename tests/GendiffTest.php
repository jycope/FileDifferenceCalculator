<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ\Differ\genDiff;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function dataProviderForJsonStylish(): array
    {
        $relativePath = [
            file_get_contents(
                __DIR__ . '/fixtures/expectedWhichPathTest.txt'
            ),
            genDiff(
                __DIR__ . '/fixtures/json/file1.json',
                __DIR__ . '/fixtures/json/file2.json'
            )
        ];

        $absolutePath = [
            file_get_contents('tests/fixtures/expectedWhichPathTest.txt'),
            genDiff('tests/fixtures/json/file1.json', 'tests/fixtures/json/file2.json')
        ];

        $emptyFiles = [
            file_get_contents('tests/fixtures/expectedWhichEmptyFiles.json'),
            genDiff('tests/fixtures/json/fileEmpty1.json', 'tests/fixtures/json/fileEmpty2.json')
        ];

        $nestedFiles = [
            file_get_contents('tests/fixtures/expectedWhichNestedFilesTest.txt'),
            genDiff('tests/fixtures/json/fileNested1.json', 'tests/fixtures/json/fileNested2.json')
        ];

        return [
            $relativePath,
            $absolutePath,
            $emptyFiles,
            $nestedFiles
        ];
    }

    /**
     * @dataProvider dataProviderForJsonStylish
     */

    public function testStylishFormat($expected, $actual): void
    {
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForYamlFormat(): array
    {
        $relativePath = [
            file_get_contents(__DIR__ . '/fixtures/expectedWhichPathTest.txt'),
            genDiff(
                __DIR__ . '/fixtures/yaml/file1.yaml',
                __DIR__ . '/fixtures/yaml/file2.yaml'
            )
        ];

        $absolutePath = [
            file_get_contents('tests/fixtures/expectedWhichEmptyFiles.json'),
            genDiff(
                'tests/fixtures/yaml/fileEmpty1.yaml',
                'tests/fixtures/yaml/fileEmpty2.yaml'
            )
        ];

        $emptyFiles = [
            file_get_contents('tests/fixtures/expectedWhichEmptyFiles.json'),
            genDiff('tests/fixtures/yaml/fileEmpty1.yaml', 'tests/fixtures/yaml/fileEmpty2.yaml')
        ];

        return [
            $relativePath,
            $absolutePath,
            $emptyFiles
        ];
    }

    /**
     * @dataProvider dataProviderForYamlFormat
     */

    public function testYamlFormat($expected, $actual): void
    {
        $this->assertEquals($expected, $actual);
    }

    public function dataProviderForJsonFormat(): array
    {
        $json = [
            file_get_contents('tests/fixtures/expectedOutputToJson.json'),
            genDiff(
                'tests/fixtures/json/file1.json',
                'tests/fixtures/json/file2.json',
                'json'
            )
        ];

        $jsonNested = [
            file_get_contents('tests/fixtures/expectedOutputToNestedJson.json'),
            genDiff(
                'tests/fixtures/json/fileNested1.json',
                'tests/fixtures/json/fileNested2.json',
                'json'
            )
        ];

        return [
            $jsonNested,
            $json
        ];
    }

    /**
     * @dataProvider dataProviderForJsonFormat
     */

    public function testJsonFormat($expected, $actual): void
    {
        $this->assertEquals($expected, $actual);
    }

    public function testPlainFormat(): void
    {
        $filePath1 = 'tests/fixtures/json/fileNested1.json';
        $filePath2 = 'tests/fixtures/json/fileNested2.json';

        $expected = file_get_contents('tests/fixtures/expectedWhichPlainFormat.txt');

        $this->assertEquals($expected, genDiff($filePath1, $filePath2, 'plain'));
    }
}
