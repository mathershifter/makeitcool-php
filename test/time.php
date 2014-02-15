<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$time = MC_Time::parse(`date`);

echo "TIME: \n";
var_dump($time->toI());

var_dump($time->w3c);


echo "Dynamic: " . $time->secondOfMinute() . "\n";
