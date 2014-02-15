<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

var_dump(MC_Math::average(array(1, 2, 3, 4, 5)));

var_dump(MC_Math::average(array(null, null, null, false)));

var_dump(MC_Math::average(array(null, null, null, 1)));



var_dump(MC_Math::avg(new MC_Array(array(0,0,0,null))));