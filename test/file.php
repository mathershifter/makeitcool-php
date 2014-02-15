<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$resource = MC_File::open('/etc/hosts');

var_dump($resource->stat);

echo $resource->read() . "\n";