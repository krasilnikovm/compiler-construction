<?php declare(strict_types=1);

use Krasilnikovm\Compiler\FileReader;
use Krasilnikovm\Compiler\Scanner;
use Krasilnikovm\Compiler\Parser;

require __DIR__ . '/../vendor/autoload.php';

$pathToCode = __DIR__ . '/../doc/code_2.txt';
$pathToRule = __DIR__ . '/../config/rule.php';

$fileReader = new FileReader($pathToCode);
$scanner = new Scanner($fileReader);
$parser = new Parser($scanner, $pathToRule);

if ($parser->parse()) {
    echo 'ok';
} else {
    echo 'fail';
}


