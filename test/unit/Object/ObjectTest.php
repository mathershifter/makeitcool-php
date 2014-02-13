<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'Mic.php'
)));

Mic::boot();

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testObject()
    {
        $obj = new Mic_Object();
        
        $this->assertEquals('Mic_Object', get_class($obj));
        
        return $obj;
    }
}