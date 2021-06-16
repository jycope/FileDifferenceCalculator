<?php

namespace Differ\differenceFiles;

// $autoloadPath1 = __DIR__ . '/../../../autoload.php';
// $autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
// if (file_exists($autoloadPath1)) {
//     require_once $autoloadPath1;
// } else {
//     require_once $autoloadPath2;
// }

use function Functional\sort;

function convertedToJson(array $array): string
{   
    $result = '';   

    foreach ($array as $key => $value) {
        $operatorChanged = explode(' ', $key)[0];
        $stateFile = $operatorChanged === ('-' || '+') ? $operatorChanged: '';

        $result .= "{$stateFile} {$key}: {$value} \n";
    }

    return "{\n" . $result . "}"; 
}

function genDiff(string $pathFile1, string $pathFile2)
{
    $json1 = json_decode(file_get_contents($pathFile1), true);
    $json2 = json_decode(file_get_contents($pathFile2), true);
    $result = [];
    $mergedFiles = array_merge($json1, $json2);

    ksort($mergedFiles);
    
    foreach ($mergedFiles as $key => $value) {
        $isKeyContainsTwoFiles = array_key_exists($key, $json1) && array_key_exists($key, $json2);
        $isKeyContainsOnlFirstFile = array_key_exists($key, $json1) && !array_key_exists($key, $json2);
        $isKeyContainsOnlySecondFile = !array_key_exists($key, $json1) && array_key_exists($key, $json2);

        $emptySecondFile = ' - ' . $key;
        $emptyFirstFile = ' + ' . $key;
        $keyEmpty = '   ' . $key;

        if ($isKeyContainsTwoFiles) {
            $valueFirstFile = $json1[$key];
            $valueSecondFile = $json2[$key];

            if ($valueFirstFile === $valueSecondFile) {
                $result[$keyEmpty] = $value;
            } elseif ($valueFirstFile !== $valueSecondFile) {
                $result[$emptySecondFile] = $value;
                $result[$emptyFirstFile] = $valueFirstFile;
            }
        } elseif ($isKeyContainsOnlySecondFile) {
            $result[$emptyFirstFile] = $value;
        } elseif ($isKeyContainsOnlFirstFile) {
            $result[$emptySecondFile] = $value;
        }
    }

    return convertedToJson($result);
}

print_r(genDiff('jsonFiles/file1.json', 'jsonFiles/file2.json'));
