<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$duration = new Mic_Duration(@$_SERVER['argv'][1] ?: 86400);

print_r($duration);

var_dump($duration->isonate());

var_dump($duration->humanize(' ', @$_SERVER['argv'][2] ?: 2));

var_dump($duration->serialize());

var_dump($duration->jsonize());