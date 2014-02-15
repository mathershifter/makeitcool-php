<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$a = new MC_Array();

print_r($a);


print_r(A(array('test'=>'wtf'))->jsonize());

var_dump(MC_Rpn::evaluate('1 1 +'));


var_dump(S('testing')->toS());


$o = O();
$o->prop = 10;
var_dump($o->kind());

new MC_Bogus();
