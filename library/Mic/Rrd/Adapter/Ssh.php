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
 * MC_Rrd_Adapter_Ssh
 *
 * Adapter class for proc_open+ssh
 *
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Adapter_Ssh extends MC_Rrd_Adapter_Abstract
{
    private $_proc;
    
    //private $authMethods = 'gssapi-keyex,gssapi-with-mic,publickey';    
    
    /**
     * SSH rrdtool adapter
     *
     * @param string $rrdtool ssh://<user>@<host><path_to_rrdtool>
     * @param array  $options
     * @throws MC_Rrd_Adapter_Exception
     */
    public function open($rrdtool, $options=array())
    {
        // make sure we are running rrdtool and not some other command
        if (!preg_match('/\/rrdtool$/', $rrdtool->path)) {
            throw new MC_Rrd_Adapter_Exception($rrdtool->path . " is not rrdtool");
        }
        
        if (!$rrdtool->host) {
            throw new MC_Rrd_Adapter_Exception("No host defined");
        }
                
        $user = $rrdtool->user ? $rrdtool->user . '@' : ''; 
        $cwd  = $options->cwd  ? $options->cwd : '/tmp';
        $env  = $options->env  ? $options->env : array();
                
        $this->_proc = proc_open(
            "ssh -oBatchMode=true $user@{$rrdtool->host} {$rrdtool->path} -",
            array(0 => array('pipe', 'r'),
                  1 => array('pipe', 'w'),
                  2 => array('pipe', 'w')),
            $this->handle, $cwd, $env);
        
        // need to wait for a second. sometimes the error doesn't show up
        // right away
        usleep(1000); 
        
        stream_set_blocking($this->handle[2], 0);
        
        if ($errstr = stream_get_contents($this->handle[2])) {
            require_once 'MC/Rrd/Adapter/Exception.php';
            throw new MC_Rrd_Adapter_Exception("Failed while opening " .
                "{$rrdtool->path}: {$errstr}");    
        }
    }
    
    public function close()
    {
        // need to close the standard resources then the 'proc' resource
        parent::close();
        
        if (is_resource($this->_proc)) {
            proc_close($this->_proc);
        }
    }
}
