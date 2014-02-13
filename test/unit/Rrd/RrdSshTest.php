<?php

require_once(join(DIRECTORY_SEPARATOR, array(
    dirname(__FILE__), 'RrdTest.php'
)));

/**
 *
 * @author jmather5
 *
 */
class RrdSshTest extends RrdTest
{
    public function testOpen()
	{
	    $identity = getenv('SSH_IDENTITY') ? "-i " . getenv('SSH_IDENTITY') : '';
	    
	    $rrd = new Mic_Rrd('ssh://cacti@localhost/usr/bin/rrdtool',
	        array('ssh' => $identity));
	    
        return $rrd;
	}
}
    