<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$ring = new Mic_Ring(5);



$ring->push(1, 2, 3, 4, 5, 6);
print_r($ring);
$ring->unshift(1);
print_r($ring);