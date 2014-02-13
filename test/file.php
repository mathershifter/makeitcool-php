<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$resource = Mic_File::open('/etc/hosts');

var_dump($resource->stat);

echo $resource->read() . "\n";