<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'Mic.php'
)));

Mic::boot();

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $obj = new Mic_Array();
        
        $this->assertEquals('Mic_Array', get_class($obj));
        
        return $obj;
    }
}