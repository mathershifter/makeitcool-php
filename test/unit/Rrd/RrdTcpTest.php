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
	    $rrd = new Mic_Rrd('tcp://localhost:13900');
	    
	    $this->assertEquals('Mic_Rrd', get_class($rrd));
        return $rrd;
	}
	
	public function testOpenFail()
	{
	    try {
            new Mic_Rrd('tcp://nonexistent:13900', array('timeout' => 1));    
	    } catch (Exception $expected) {
            return;
        }
	    
        $this->fail('An expected exception has not been raised.');
	}
}