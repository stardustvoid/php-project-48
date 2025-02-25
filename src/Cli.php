<?php

namespace Differ\Cli;

use function Differ\Differ\genDiff;

function differCli($doc)
{
    $args = \Docopt::handle($doc, array('version' => 'Differ 1.0'));

    $fileOnePath = $args['<firstFile>'];
    $fileTwoPath = $args['<secondFile>'];

    $format = $args['--format'];

    print_r(genDiff($fileOnePath, $fileTwoPath, $format));
}
