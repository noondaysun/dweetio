<?php
namespace Noondaysun\Dweetio;

/**
 * Example usage of Dweetio oject
 */

// : Includes
require_once dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Dweetio.php';
// : End
$dweet = new \Noondaysun\Dweetio\Dweetio_Client();

$thing = (string) 'temperature';
if (array_key_exists(1, $argv)) {
    $thing = $argv[1];
}

$dweet->setThing($thing);

$latest = $dweet->getLatestDweetFor();
var_dump($latest);

sleep(5);

$dweets = $dweet->getDweetsFor();
var_dump($dweets);

sleep(5);

$content = (array) [
    'test' => 'number'
];
$dweet->setContent($content);
$success = $dweet->dweetFor(false);
var_dump($success);

sleep(5);

