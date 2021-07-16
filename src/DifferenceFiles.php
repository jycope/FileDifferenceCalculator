<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Differ\convertedToJson;

function getProperties($array)
{
    if (!is_array($array)) {        
        return $array;
    }

    $properties = array_map(fn($child) => getProperties($child), $array);

    return $properties;
}

function convertedToJson($array)
{
    $filtered = array_filter($array, fn($child) => is_array($child));

    $result = array_map(function ($child) {
        $key = key($child);
        $child = str_replace(["\"", ","], "", json_encode(
            getProperties($child),
            JSON_PRETTY_PRINT
        ));

        if (is_array(getProperties($child))) {
            return <<<EOT
            $key: {
                $child
            }
            EOT;
        } elseif (is_string(getProperties($child))) {
            $operatorChanged = explode(":", $key)[0];
            
            switch ($operatorChanged) {
                case '-':
                case '+':
                    $child = str_replace("}", "  }", $child);
                default:
                    break;
            }

            return <<<EOT
            $key: $child
            EOT;
        }
    }, $filtered);

    return $result;
}

function genDiff($pathFile1, $pathFile2, $format = "stylish")
{
    switch ($format) {
        case 'plain':
            return formattedPlain($pathFile1, $pathFile2, $format);
        case 'json':
            return convertedToJson(formattedJson($pathFile1, $pathFile2, $format));
        default:
            return convertedToJson(formattedDefault($pathFile1, $pathFile2, $format));
    }
}
