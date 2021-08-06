<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Formatters\addOperatorToKeys;
use function Differ\Parsers\getDataFromFile;

const FORMAT_FLAGS = 
    JSON_NUMERIC_CHECK |
    JSON_FORCE_OBJECT |
    JSON_PRESERVE_ZERO_FRACTION |
    JSON_UNESCAPED_SLASHES |
    JSON_UNESCAPED_UNICODE |
    JSON_PRETTY_PRINT;

function convertingArrayToJson($data, $replacer = " ", $count = 2): string
{
    $getJsonRepresantion = function ($result, $value, $key) use ($replacer, $count, &$getJsonRepresantion) {
        $indent = str_repeat($replacer, $count);
        $lineEnd = "\n";

        if (is_array($value)) {
            $firstSymbols = explode(" ", $key)[0];
            $isSymboldChanged = $firstSymbols === "-" || $firstSymbols === "+" || $firstSymbols === "*";
            $indentForBracket = $isSymboldChanged ? str_repeat($replacer, $count + 2) : $indent;

            $valueElemForJson = $getJsonRepresantion($value, $replacer, $count + 4);

            $result[] = $indent . $key . ": {\n" . $valueElemForJson . "}" . "\n";
        } elseif (!is_array($value)) {
            $value = var_export($value, true);
            $result[] = $indent . $key . ": " . $value . "\n";
        }

        return $result;
    };

    return collect($data)->reduce($getJsonRepresantion, []);
}

function clearedData(string $data, $format = 'stylish'): string
{
    $search  =  [];
    $replace =  [];

    switch ($format) {
        case 'plain':
            $search  = 'NULL';
            $replace = 'null';
            break;
        default:
            $search =  ['* ', '\'', 'NULL'];
            $replace = ['  ', '', 'null'];
            break;
    }

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
            $dataFormatted = collect(formattedPlain($data1, $data2))->flatten()->join("\n");
            return clearedData($dataFormatted, $format);
        case 'json':
            $jsonNotCleared = json_encode(
                formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)),
                FORMAT_FLAGS
            );
            
            return clearedData($jsonNotCleared);
        case "stylish":
            return formattedDataToJsonStr(formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)));
    }
}
