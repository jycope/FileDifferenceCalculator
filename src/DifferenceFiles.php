<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Formatters\addOperatorToKeys;
use function Differ\Parsers\getDataFromFile;

function convertingArrayToJson($data, $replacer = " ", $count = 2): string
{
    $result = "";
    $indent = str_repeat($replacer, $count);
    $lineEnd = "\n";

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $firstSymbols = explode(" ", $key)[0];
            $isSymboldChanged = $firstSymbols === "-" || $firstSymbols === "+" || $firstSymbols === "*";
            $indentForBracket = $isSymboldChanged ? str_repeat($replacer, $count + 2) : $indent;

            $valueElemForJson = convertingArrayToJson($value, $replacer, $count + 4) . $indentForBracket;

            $result .= $indent . $key . ": {\n" . $valueElemForJson . "}" . "\n";
        } elseif (!is_array($value)) {
            $value = var_export($value, true);
            $result .= $indent . $key . ": " . $value . "\n";
        }
    }
    return $result;
}

function clearedData(string $data): string
{
    $search  =  ['* ', '\'', 'NULL'];
    $replace =  ['  ', '', 'null'];

    return str_replace($search, $replace, $data);
}

function formattedDataToJsonStr(array $data, $format = "default")
{
    $json = convertingArrayToJson($data);

    return "{\n" . clearedData($json) . "}";
}

function genDiff($pathFile1, $pathFile2, $format = "stylish")
{
    $data1 = getDataFromFile($pathFile1);
    $data2 = getDataFromFile($pathFile2);

    switch ($format) {
        case 'plain':
            $plainFormattedData = str_replace('NULL', 'null', formattedPlain($data1, $data2));
            return substr($plainFormattedData, 1);
        case 'json':
            $jsonNotCleared = json_encode(
                formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)),
                JSON_NUMERIC_CHECK |
                JSON_FORCE_OBJECT |
                JSON_PRESERVE_ZERO_FRACTION |
                JSON_UNESCAPED_SLASHES |
                JSON_UNESCAPED_UNICODE |
                JSON_PRETTY_PRINT
            );

            return clearedData($jsonNotCleared);
        case "stylish":
            return formattedDataToJsonStr(formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)));
    }
}
