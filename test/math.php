<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

var_dump(Mic_Math::average(array(1, 2, 3, 4, 5)));

var_dump(Mic_Math::average(array(null, null, null, false)));

var_dump(Mic_Math::average(array(null, null, null, 1)));



var_dump(Mic_Math::avg(new Mic_Array(array(0,0,0,null))));