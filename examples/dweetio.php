<?php
namespace Noondaysun\Dweetio;

/**
 * Example usage of Dweetio oject
 */

// : Includes
include_once dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Dweetio.php';
// : End
$dweet = new \Noondaysun\Dweetio\Dweetio_Client();

$thing = (string) $argv[1] ?? 'temperature';

$latest = $dweet->getLatestDweetFor($thing);
var_dump($latest);

$dweets = $dweet->getDweetsFor($thing);
var_dump($dweets);

$content = (array) [
    'test' => 'number'
];
$success = $dweet->dweetFor($thing, $content, false);
var_dump($success);
