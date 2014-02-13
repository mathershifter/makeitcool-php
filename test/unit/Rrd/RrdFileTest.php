<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), 'RrdTest.php'
)));
/**
 *
 * @author jmather5
 *
 */
class RrdFileTest extends RrdTest
{
    protected $readOnly = false;
    
    public function testOpen()
	{
	    
	    $rrd = new Mic_Rrd('file:///usr/local/rrdtool-1.2.19/bin/rrdtool');
	    
        return $rrd;
	}
}