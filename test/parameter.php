<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

print_r( Mic_Parameter::map() );