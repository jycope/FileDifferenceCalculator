<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;
use Differ\differenceFiles\genDiff;

use function Differ\differenceFiles\genDiff;

class GendiffTest extends TestCase
{
    public function testDifferenceFilesRelativePath()
    {
        $filePath1 = 'jsonFiles/file1.json';
        $filePath2 = 'jsonFiles/file2.json';

        $expected =
            '{
                - follow: false
                  host: hexlet.io
                - proxy: 123.234.53.22
                - timeout: 50
                + timeout: 20
                + verbose: true
            }';

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    public function testDifferenceFilesAbsolutePath()
    {
        $filePath1 = 'jsonFiles/file1.json';
        $filePath2 = 'jsonFiles/file2.json';

        $expected =
            '{
                - follow: false
                  host: hexlet.io
                - proxy: 123.234.53.22
                - timeout: 50
                + timeout: 20
                + verbose: true
            }';

        $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    }

    // public function testDifferenceFilesNonexistentPath()
    // {
    //     $filePath1 = '/json/file1.json';
    //     $filePath2 = '/json/file2.json';

    //     $expected =
    //         '{
    //             - follow: false
    //               host: hexlet.io
    //             - proxy: 123.234.53.22
    //             - timeout: 50
    //             + timeout: 20
    //             + verbose: true
    //         }';

    //     $this->expectException(Exception::class);

    //     $this->assertEquals($expected, genDiff($filePath1, $filePath2));
    // }
}
