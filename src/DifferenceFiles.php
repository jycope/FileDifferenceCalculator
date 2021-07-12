<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\formattedDefault;
use function Differ\Formatters\formattedPlain;
use function Differ\Formatters\formattedJson;
use function Differ\Differ\convertedToJson;

function convertedToJson(array $array)
{
    return str_replace(["\"", ","], "", json_encode(
        $array,
        JSON_NUMERIC_CHECK |
        JSON_FORCE_OBJECT |
        JSON_PRESERVE_ZERO_FRACTION |
        JSON_UNESCAPED_SLASHES |
        JSON_UNESCAPED_UNICODE |
        JSON_PRETTY_PRINT
    ));
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
