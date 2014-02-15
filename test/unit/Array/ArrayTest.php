<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'MC.php'
)));

MC::boot();

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testArray()
    {
        $obj = new MC_Array();
        
        $this->assertEquals('MC_Array', get_class($obj));
        
        return $obj;
    }
}