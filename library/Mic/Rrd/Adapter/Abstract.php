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
 * @version   SVN: $Id: Abstract.php 1094 2010-06-22 00:10:35Z jmather5 $
 * @filesource
 */

/**
 * MC_Rrd_Adapter_Abstract
 *
 * @category   MC
 * @package    MC_Rrd
 */
abstract class MC_Rrd_Adapter_Abstract
{
    /**
     * Reference to rrdtool resource
     *
     * @var MC_Rrd_Adapter_Abstract
     */
    protected $handle;
    
    /**
     * Create a new adapter and open the resource
     *
     * @param object $rrdtool DataType_Collection for parsed URI
     * @param array  $options any options to pass to the open method
     */
    public function __construct($rrdtool, $options=array())
    {
        $this->open($rrdtool, $options);
    }
    
    /**
     * Makes sure rrdtool resource is closed when object is destroyed
     */
    public function __destruct()
    {
        $this->close();
    }
    
    /**
     * Get resources for stdin, stdout, and stderr
     *
     * @return array $pipes
     */
    private function _getPipes()
    {
        $pipes = array();
        
        // when handle is an array assume indexes are 0 for stdin, 1 for stdout,
        // and 2 for stdin.
        if (is_array($this->handle)) {
            $pipes = $this->handle;
        } else { // this handle is assumed to be capable of 2-way + errors communication
            $pipes = array(&$this->handle, &$this->handle, &$this->handle);
        }
        
        return $pipes;
    }
    
    /**
     * Send a command to rrdtool and return the response
     *
     * @param string $command
     * @param array  $options
     * @throws MC_Rrd_Adapter_Exception
     * @return array $response
     */
    public function read($command, $options=array())
    {
        $response = null;
        
        $pipes = $this->_getPipes();
        
        // make sure pipes are valid
        if (!is_resource($pipes[0]) || !is_resource($pipes[1])) {
            require_once 'MC/Rrd/Adapter/Exception.php';
            throw new MC_Rrd_Adapter_Exception("Pipe is not a valid resource");
        }
        
        if (!empty($options)) {
            $command .= ' ' . join(' ', $options);
        }
        
        // send the command to rrdtool
        fwrite($pipes[0], "$command\r\n");
        
        // read until 'OK', 'ERROR', or EOF from rrdtool
        while (!feof($pipes[1])) {
            $line = trim(fgets($pipes[1], 4096));
            
            // OK singals the command has completed
            if (preg_match('/^OK\s.*/', $line)) {
                break;
            }
            
            // eject if an error occurs
            if (preg_match('/^ERROR\:\s+(.*)/', $line, $matches)) {
                require_once 'MC/Rrd/Adapter/Exception.php';
                throw new MC_Rrd_Adapter_Exception("RRD Error: {$matches[1]} in '$command'");
            }
            
            $response[] = $line;
        }
        
        return $response;
    }

    /**
     * Close the rrdtool resource
     */
    public function close()
    {
        try {
            $this->read('quit');
        } catch(Exception $e) {/* don't care here */}           
             
        // make sure to cleanup all remaining resources
        foreach ($this->_getPipes() as $pipe) {
            if (is_resource($pipe)) fclose($pipe);
        }
    }

    /**
     * Open the rrdtool resource
     *
     * @abstract
     * @param string $command the command
     * @param array  $args    parameters for rrdtool command
     */
    abstract protected function open($rrdtool, $options=array());
}
