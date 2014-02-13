<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Rrd
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: Tcp.php 801 2010-03-24 22:55:35Z jmather5 $
 * @filesource
 */

/**
 * @see Mic_Rrd_Adapter_Abstract
 */
require_once 'Mic/Rrd/Adapter/Abstract.php';

/**
 * Mic_Rrd_Adapter_Tcp
 *
 * Establishes a connection to an RRD server running on a remote system
 * 
 * @category   Mic
 * @package    Mic_Rrd
 */
class Mic_Rrd_Adapter_Tcp extends Mic_Rrd_Adapter_Abstract
{
    /**
     * TCP (rrdsrv) rrdtool adapter
     *
     * @param string $rrdtool tcp://<host>[:<port>]
     * @param array  $options
     * @throws Mic_Rrd_Adapter_Exception
     */
    public function open($rrdtool, $options=array())
    {   
        // use the default timeout if not specified
        $timeout = $options->timeout > 0 ? $options->timeout : 5;
        
        // use the default port if not specified
        $port    = $rrdtool->port > 0    ? $rrdtool->port    : 13900;
        
        $this->handle = @fsockopen($rrdtool->host, $port,
            $errno, $errstr, $timeout);
        
        if (!$this->handle) {
            require_once 'Mic/Rrd/Adapter/Exception.php';
            throw new Mic_Rrd_Adapter_Exception("Failed while opening " .
                "{$rrdtool->host}:$port - $errstr [$errno]");
        }
    }
}
