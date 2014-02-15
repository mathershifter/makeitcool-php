<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'MC.php'
)));

MC::boot();

class ObjectTest extends PHPUnit_Framework_TestCase
{
    public function testObject()
    {
        $obj = new MC_Object();
        
        $this->assertEquals('MC_Object', get_class($obj));
        
        return $obj;
    }
}