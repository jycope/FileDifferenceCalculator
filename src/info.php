<?php

namespace Differ\info;

use Docopt;

use function Differ\differenceFiles\genDiff;

function getInfo()
{
    $doc = <<<DOC
    Generate diff
    
    Usage:
        gendiff (-h|--help)
        gendiff (-v|--version)
        gendiff [--format <fmt>] <firstFile> <secondFile>
        
    Options:
        -h --help                     Show this screen
        -v --version                  Show version
        --format <fmt>                Report format [default: stylish]
    DOC;

    $args = Docopt::handle($doc, array('version' => 'Naval Fate 2.0'));

    $filePath1 = $args['<firstFile>'];
    $filePath2 = $args['<secondFile>'];

    return genDiff($filePath1, $filePath2);
}
