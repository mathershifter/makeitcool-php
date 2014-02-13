<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$string = new S('testString');
print_r($string);
echo $string->underscorize()->upper() . "\n";

echo S::create('Another Test String')->underscorize() . "\n";
