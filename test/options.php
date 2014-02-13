<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

$parser = new Mic_Console_OptionParser;

$parser->addRule('t|test::', "Test rule");
$parser->addRule('d|debug', "Debug rule");
$parser->addRule('w|two-words', "Two words rule");
$parser->addRule('v:', "Debug rule");

$parser->parse();



print_r($parser);


print_r($parser->toArray());