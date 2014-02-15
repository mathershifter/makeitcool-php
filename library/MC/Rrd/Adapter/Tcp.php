<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Rrd
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @filesource
 */

/**
 * @see MC_Rrd_Adapter_Abstract
 */
require_once 'MC/Rrd/Adapter/Abstract.php';

/**
 * MC_Rrd_Adapter_Tcp
 *
 * Establishes a connection to an RRD server running on a remote system
 * 
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Adapter_Tcp extends MC_Rrd_Adapter_Abstract
{
    /**
     * TCP (rrdsrv) rrdtool adapter
     *
     * @param string $rrdtool tcp://<host>[:<port>]
     * @param array  $options
     * @throws MC_Rrd_Adapter_Exception
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
            require_once 'MC/Rrd/Adapter/Exception.php';
            throw new MC_Rrd_Adapter_Exception("Failed while opening " .
                "{$rrdtool->host}:$port - $errstr [$errno]");
        }
    }
}
