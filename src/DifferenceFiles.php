<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Formatters\addOperatorToKeys;
use function Differ\Parsers\getDataFromFile;

function convertingArrayToJson($data, $replacer = " ", $count = 2, $lineEnd = "\n", $isQuoteAroundTheKey = false): string
{
    $result = "";
    $indent = str_repeat($replacer, $count);
    $wrapTheValueInQuotes = fn ($value) => $isQuoteAroundTheKey ? "\"{$value}\"" : $value;

    foreach ($data as $key => $value) {
        $key = $wrapTheValueInQuotes($key);
        
        if (is_array($value)) {
            $firstSymbols = explode(" ", $key)[0];
            $isSymboldChanged = $firstSymbols === "-" || $firstSymbols === "+" || $firstSymbols === "*";
            $indentForBracket = $isSymboldChanged  ? str_repeat($replacer, $count + 2) : $indent;

            $result .= $indent . $key . ": {\n" . convertingArrayToJson($value, $replacer, $count + 4, $lineEnd) . $indentForBracket . "}" . $lineEnd;
        } elseif (!is_array($value)) {
            $value = $wrapTheValueInQuotes(var_export($value, true));
            
            $result .= $indent . $key . ": " . $value . $lineEnd;
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
    $json = "";

    switch ($format) {
        case 'json':
            $json = convertingArrayToJson($data, " ", 2, ",\n", true);
            break;
        default:
            $json = convertingArrayToJson($data);
            break;
    }

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
            return formattedDataToJsonStr(formattedJson($data1, $data2), $format);
        case "stylish":
            return formattedDataToJsonStr(formattedDefault(addOperatorToKeys($data1), addOperatorToKeys($data2)));
    }
}
