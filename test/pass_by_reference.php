<?php

class Person
{
    public $name = 'Jesse';
}


$person = new Person();

print_r($person);


function change_name(Person $person, $new_name)
{
    $person->name = $new_name;
}

change_name($person, 'Alton');

print_r($person);