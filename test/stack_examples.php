<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

#$stack = new Mic_Type_Stack(array(), 5);
#$stack->push(array(10, 11, 12), 13 , 14, 15, array(array(16, 17, 18, 'test' => 19)));
#print_r($stack->toArray());

$stack = new Mic_Stack(1);


print_r($stack);