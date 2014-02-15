<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$template = "I am the {rank} of {rank}s.\n";

$replacements = array('rank' => 'king');

var_dump(MC_Template::process($template, $replacements));