<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

var_dump(Mic_Rpn::evaluate(array(3, 3, '*')));
var_dump(Mic_Rpn::evaluate(array(3, 5, null, '+', '+')));
var_dump(Mic_Rpn::evaluate(array(0xffeeff, 5, 4, '+', '+')));
var_dump(Mic_Rpn::evaluate(array(5, 1, 10, 'LIMIT')));
var_dump(Mic_Rpn::evaluate(array(5, 0, '^')));