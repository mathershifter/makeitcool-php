<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$array = A(array('test', 'value', 'exists', 10));

echo ($array->contains('test') ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains('foo') ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains(10) ? 'TRUE' : 'FALSE') . "\n";

echo ($array->contains('10') ? 'TRUE' : 'FALSE') . "\n";

echo "\n\n5.3 features....\n\n";

//var_dump(MC_Array(array(1,2,3,4)));