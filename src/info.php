<?php

namespace Differ\info;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use Docopt;

const DOC = <<<DOC
    Usage:
        gendiff (-h|--help)
        gendiff (-v|--version)
        gendiff [--format <fmt>] <firstFile> <secondFile>
        
    Options:
        -h --help                     Show this screen
        -v --version                  Show version
        --format <fmt>                Report format [default: stylish]
DOC;

function getInfo()
{
    $args = Docopt::handle(DOC, array('version' => 'Naval Fate 2.0'));

    return $args;
}
