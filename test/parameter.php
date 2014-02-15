<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

print_r( MC_Parameter::map() );