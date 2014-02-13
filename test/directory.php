<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$resource = Mic_Directory::open('.');

var_dump($resource);

print_r($resource->scan('/[^\.]/')) . "\n";