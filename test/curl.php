<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();
$get = Mic_Http::open('http://some-web-service.mathershifter.com/api/v1/infrastructure/nodes/<hostname>.json')->get();

echo "\n\n";

echo "CODE: " . $get->response . "\n";