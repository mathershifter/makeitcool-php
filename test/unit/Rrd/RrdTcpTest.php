<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), 'RrdTest.php'
)));

/**
 *
 * @author jmather5
 *
 */
class RrdTcpTest extends RrdTest
{
    public function testOpen()
	{
	    $rrd = new MC_Rrd('tcp://localhost:13900');
	    
	    $this->assertEquals('MC_Rrd', get_class($rrd));
        return $rrd;
	}
	
	public function testOpenFail()
	{
	    try {
            new MC_Rrd('tcp://nonexistent:13900', array('timeout' => 1));    
	    } catch (Exception $expected) {
            return;
        }
	    
        $this->fail('An expected exception has not been raised.');
	}
}