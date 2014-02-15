<?php
require_once dirname(__FILE__) . '/../library/MC.php';
MC::boot();

$data = A(array(
    array(
        'name' => 'Jesse'
    ),
    array(
        'name' => 'Alton'
    ),
    array(
        'name' => 'Carmen'
    )
));

print_r($data);

$data->sort('callback');

print_r($data->toArray());

function callback($a, $b)
{
    return strnatcmp($a->name, $b->name);
}

