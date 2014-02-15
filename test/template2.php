<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$template = "a.name: {a.name} , b.name: {b.name} testCamel: {testCamel} test_under {test_under} duplicate: {a.name}";

$replacements = array(
    'a' => array('name' => 'Jesse'),
    
    'b' => array('name' => 'Josh'),
    
    'testCamel' => 'camelTest',
    
    'test_under' => 'under_test'
);

var_dump(MC_Template2::process($template, $replacements));