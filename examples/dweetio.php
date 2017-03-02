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
// $latest = $dweet->getLatestDweetFor($thing);
// $dweets = $dweet->getDweetsFor($thing);

$content = (array) [
    'test' => 'number'
];
$success = $dweet->dweetFor($thing, $content, false);
print_r($success);
