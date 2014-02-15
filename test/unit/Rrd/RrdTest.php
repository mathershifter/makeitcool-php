<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), '..' , '..', '..', 'library', 'MC.php'
)));

MC::boot();

require_once 'PHPUnit/Framework.php';

/**
 *
 * @author jmather5
 *
 */
class RrdTest extends PHPUnit_Framework_TestCase
{
    protected $readOnly    = true;
    protected $testRrdFile = '/tmp/_rra/temp.rrd';
        
    public function testOpen()
	{
        return false;
	}
	
	/**
     * @depends testOpen
     */
	public function testMkdir($rrd)
    {
        if ($this->readOnly) {
            $this->markTestSkipped('Test is marked read-only');
        }
        
        try {
            $this->assertTrue(
                $rrd->mkdir(dirname($this->testRrdFile))
                    ->response()
                    ->isNull()
            );
        } catch (Exception $e) { // a file exists error is OK
            $this->assertRegExp('/File exists/', $e->getMessage());
        }
    }
	
    /**
     * @depends testOpen
     */
    public function testCd($rrd)
    {
        $this->assertTrue(
            $rrd->cd(dirname($this->testRrdFile))
                ->response()
                ->isNull()
        );
    }
    
	/**
     * @depends testOpen
     */
	public function testPwd($rrd)
    {
        $this->assertEquals(dirname($this->testRrdFile),
            $rrd->pwd()
                ->response()  // get the response
                ->cast()         // cast as native php type
        );
    }
    
    /**
     * @depends testOpen
     */
    public function testLs($rrd)
    {
        $this->assertEquals('DataType_Collection',
            $rrd->ls()       // run the command
                ->response() // get the response
                ->instance() // get the response class
        );
    }
    
    /**
     * @depends testOpen
     */
    public function testCreate($rrd)
    {
        if ($this->readOnly) {
            $this->markTestSkipped('Test is marked read-only');
        }
        
        // create operation should return a null object
        $this->assertTrue(
            $rrd->create(
                $this->testRrdFile, "--step 300", "DS:temp:GAUGE:600:-273:5000",
                    "RRA:AVERAGE:0.5:1:1200", "RRA:MIN:0.5:12:2400",
                    "RRA:MAX:0.5:12:2400", "RRA:AVERAGE:0.5:12:2400")
                ->response()
                ->isNull()
        );
    }
    
    /**
     * @depends testOpen
     */
    public function testUpdate($rrd)
    {
        if ($this->readOnly) {
            $this->markTestSkipped('Test is marked read-only');
        }
        
        $this->assertTrue(
            $rrd->update($this->testRrdFile, 'N:' . rand(-273, 5000))
                ->response()
                ->isNull()
        );
    }
    
    /**
     * @depends testOpen
     */
    public function testFetch($rrd)
    {
        $this->assertEquals('DataType_Collection',
            $rrd->fetch($this->testRrdFile, 'AVERAGE', '-s -3600')
                ->response() // get the response
                ->instance() // get the response class
        );
    }
}