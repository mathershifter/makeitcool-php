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
 * @version   SVN: $Id: File.php 801 2010-03-24 22:55:35Z jmather5 $
 * @filesource
 */

/**
 * MC_Rrd_Adapter_Abstract
 */
require_once 'MC/Rrd/Adapter/Abstract.php';

/**
 * MC_Rrd_Adapter_File
 *
 * Adapter class for local rrdtool proc_open
 *
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Adapter_File extends MC_Rrd_Adapter_Abstract
{
    private $proc;
    
    /**
     * Local proc rrdtool adapter
     *
     * @param string $rrdtool [file://]/usr/bin/rrdtool
     * @param array  $options
     * @throws MC_Rrd_Adapter_Exception
     */
    public function open($rrdtool, $options=array())
    {
        // make sure we are running rrdtool and not some other command
        if (!preg_match('/\/rrdtool$/', $rrdtool->path)) {
            throw new Exception("$rrdtool is not rrdtool");
        }
        
        $cwd  = $options->cwd  ? $options->cwd : '/tmp';
        $env  = $options->env  ? $options->env->toArray() : array();
        
        $this->proc = proc_open(
            $rrdtool->path . ' -',
            array(0 => array('pipe', 'r'),
                  1 => array('pipe', 'w'),
                  2 => array('pipe', 'w')),
            $this->handle, $cwd, $env
        );
        
        // need to wait for a second. sometimes the error doesn't show up
        // right away
        usleep(1000);
        
        stream_set_blocking($this->handle[2], 0);
        
        if ($errstr = stream_get_contents($this->handle[2])) {
            require_once 'MC/Rrd/Adapter/Exception.php';
            throw new MC_Rrd_Adapter_Exception("Failed while opening {$rrdtool}: {$errstr}");    
        }
    }
    
    public function close()
    {
        // need to close the standard resources then the 'proc' resource
        parent::close();
        if (is_resource($this->proc)) proc_close($this->proc);
    }
}
