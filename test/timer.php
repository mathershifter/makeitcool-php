<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$timer = new MC_Timer;
$result = $timer->measure(function ($a, $b) {
    sleep(1);
    return "$a $b";
}, array('test'), 'test2');

$duration = $timer->getDuration();

echo "$result - Measured: $duration\n";