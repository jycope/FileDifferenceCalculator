<?php

namespace Differ\differenceFiles;

use function Functional\sort;

function convertedToJson(array $array): string
{
    $result = '';

    foreach ($array as $key => $value) {
        $operatorChanged = explode(' ', $key)[1];

        $stateFile = $operatorChanged === ('-' || '+') ? $operatorChanged : '';

        $result .= "{$stateFile} {$key}: {$value}\n";
    }

    return "{\n" . $result . "}"; 
}

function genDiff(string $pathFile1, string $pathFile2): string
{
    $json1 = json_decode(file_get_contents($pathFile1), true);
    $json2 = json_decode(file_get_contents($pathFile2), true);
    $result = [];
    $mergedFiles = array_merge($json1, $json2);

    ksort($mergedFiles);

    foreach ($mergedFiles as $key => $value) {
        $value = is_bool($value) ? ($value === true ? 'true' : 'false') : $value;

        $isKeyContainsTwoFiles = array_key_exists($key, $json1) && array_key_exists($key, $json2);
        $isKeyContainsOnlFirstFile = array_key_exists($key, $json1) && !array_key_exists($key, $json2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $json1) && array_key_exists($key, $json2);

        $emptySecondFileValue = ' - ' . $key;
        $emptyFirstFileValue = ' + ' . $key;
        $keyEmpty = '   ' . $key;

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $json1[$key];
            $valueSecondFile = $json2[$key];

            if ($valueFirstFile === $valueSecondFile) {
                $result[$keyEmpty] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFileValue] = $valueFirstFile;
                $result[$emptyFirstFileValue] = $value;
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFileValue] = $value;
        } elseif ($isKeyContainsOnlFirstFile) {
            $result[$emptySecondFileValue] = $value;
        }
    }

    return convertedToJson($result);
}

print_r(genDiff('tests/fixtures/fileEmpty1.json', 'tests/fixtures/fileEmpty2.json'));