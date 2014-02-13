<?php
require_once dirname(__FILE__) . '/../library/Mic.php';
Mic::boot();

echo O()->send('can', 'shoot');

echo O()->send('can', 'jsonize');

echo O()->send('can', 'serialize');