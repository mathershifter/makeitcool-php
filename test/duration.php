<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$duration = new MC_Duration(@$_SERVER['argv'][1] ?: 86400);

print_r($duration);

var_dump($duration->isonate());

var_dump($duration->humanize(' ', @$_SERVER['argv'][2] ?: 2));

var_dump($duration->serialize());

var_dump($duration->jsonize());