<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

echo O()->send('can', 'shoot');

echo O()->send('can', 'jsonize');

echo O()->send('can', 'serialize');