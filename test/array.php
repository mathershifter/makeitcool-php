<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$array = A(array('test', 'value', 'exists', 10));

echo ($array->contains('test') ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains('foo') ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains(10) ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains('10') ? 'TRUE' : 'FALSE') . "\n";

echo "\n\n5.3 features....\n\n";

//var_dump(Mic_Array(array(1,2,3,4)));