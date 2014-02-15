<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'MC.php'
)));

MC::boot();


class StringTest extends PHPUnit_Framework_TestCase
{
    private $_testString = "Test this string ";
    public function testString()
    {
        $string = new MC_String($this->_testString);
        
        $this->assertEquals('MC_String', get_class($string));
        
        return $string;
    }
    
    /**
     * @depends testString
     */
    public function testToString($string)
    {
        $this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_STRING, "$string");
    }
    
    /**
     * @depends testString
     */
    public function testCamelize($string)
    {
        $this->assertEquals("testThisString", "{$string->camelize()}");
    }
    
    /**
     * @depends testString
     */
    public function testClassify($string)
    {
        $this->assertEquals("TestThisString", "{$string->classify()}");
    }
    
    /**
     * @depends testString
     */
    public function testCapitalize($string)
    {
        $this->assertEquals(ucfirst($this->_testString), "{$string->capitalize()}");
    }
    
    /**
     * @depends testString
     */
    public function testGrep($string)
    {
        $this->assertFalse($string->grep('/funky/')); 
        $this->assertEquals('this', "{$string->grep('/this/')}"); 
        $this->assertEquals(3, $string->grep('/(Test) this (string)/')->count());
    }
    
    /**
     * @depends testString
     */
    public function testHumanize($string)
    {
        $this->assertEquals("Test This String", "{$string->humanize()}");
    }
        
    /**
     * @depends testString
     */
    public function testHex($string)
    {
        $hex = new MC_String("74657374207468697320737472696e6720");
        $this->assertEquals("$hex", "{$string->hex()}");
        $this->assertFalse($string->isHex());
        $this->assertTrue($hex->isHex());
    }
    
    /**
     * @depends testString
     */
    public function testLower($string)
    {
        $this->assertEquals(strtolower($this->_testString), "{$string->lower()}");
    }
    
    /**
     * @depends testString
     */
    public function testMatch($string)
    {
    }
    
    /**
     * @depends testString
     */
    public function testReplace($string)
    {
    }
    
    /**
     * @depends testString
     */
    public function testRsplit($string)
    {
    }
    
    /**
     * @depends testString
     */
    public function testSlice($string)
    {
    }
    
    /**
     * @depends testString
     */
    public function testSplice($string)
    {
    }
    /**
     * @depends testString
     */
    public function testTrim(MC_String $string)
    {
        $this->assertEquals(trim($this->_testString), "{$string->trim()}");
    }
    
    /**
     * @depends testString
     */
    public function testUnderscorize()
    {
        
    }
    
    /**
     * @depends testString
     */
    public function testUnhex()
    {
        
    }
    
    /**
     * @depends testString
     */
    public function testUpper()
    {
        
    }
}