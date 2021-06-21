<?php

namespace Differ\info;

use Docopt;


function getInfo()
{
    $doc = <<<DOC
        Generate diff

        Usage:
            gendiff (-h|--help)
            gendiff (-v|--version)
            gendiff <firstFile> <secondFile>
            
        Options:
            -h --help                     Show this screen
            -v --version                  Show version
            --format <fmt>                Report format [default: stylish]
    DOC;

    $args = Docopt::handle($doc, array(
        'version' => null,
        'help' => true,
    ));
    
    return $args;
}