<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$template = "I am the {rank} of {rank}s.\n";

$replacements = array('rank' => 'king');

var_dump(Mic_Template::process($template, $replacements));