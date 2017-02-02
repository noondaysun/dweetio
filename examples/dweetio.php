<?php
namespace Noondaysun\Dweetio;
//: Includes
include_once dirname(realpath( __FILE__ )) . DIRECTORY_SEPARATOR . '..' . 
DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Dweetio.php';
//: End
$dweet = new \Noondaysun\Dweetio\Dweetio_Client();

$thing = (string) 'temperature';
$latest = $dweet->getLatestDweetFor($thing);
$dweets = $dweet->getDweetsFor($thing);
print_r($dweets);
