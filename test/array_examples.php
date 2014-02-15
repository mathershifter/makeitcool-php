<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$array = A(array('lame' => 'test0', 'test', 'test2', 10));

print_r($array);

foreach ($array as $key=>$val) {
    echo $key . "=>" . $val . "\n";
}

echo $array['lame'] . "\n";
//echo $array[2] . "\n";
echo gettype($array['lamer']) . "\n";

echo $array . "\n";

$funky_array = A(array('not_lame' => 'really?'))->merge($array)->merge(array('wow' => 'not really?'))->merge((object) array('not wow?' => 'not not really!'));
// test merging different type of data
print_r($funky_array);


print_r((array) $funky_array);