<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();
$get = MC_Http::open('http://some-web-service.mathershifter.com/api/v1/infrastructure/nodes/<hostname>.json')->get();

echo "\n\n";

echo "CODE: " . $get->response . "\n";