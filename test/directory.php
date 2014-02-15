<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$resource = MC_Directory::open('.');

var_dump($resource);

print_r($resource->scan('/[^\.]/')) . "\n";