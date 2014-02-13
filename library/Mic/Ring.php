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
 * @package   Mic_Ring
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @deprecated
 * @filesource
 */

/**
 * @see Mic_Array 
 */
require_once 'Mic/Array.php';

trigger_error(__FILE__ . " is deprecated", E_USER_WARNING);

/**
 * Mic_Ring
 *
 * @category  Mic
 * @package   Mic_Ring
 */
class Mic_Ring extends Mic_Array 
{
    private $_buffer = null;
    
    /**
     * 
     */
    protected function _init($args)
    {
        $buffer = array_shift($args);
        
        echo "BUFFER: $buffer\n";
        
        if (!is_integer($buffer)) {
            require_once 'Mic/Ring/Exception.php';
            throw new Mic_Ring_Exception("Invalid buffer: must be an integer");
            
        } elseif ($buffer < 1) {
            require_once 'Mic/Ring/Exception.php';
            throw new Mic_Ring_Exception("Ring size must be greater than 0");
        } else {
            $this->_buffer = $buffer;
        }
        
        return array();
    }
    
    /**
     * 
     */
    public function __set($name, $val)
    {
        require_once 'Mic/Ring/Exception.php';
        throw new Mic_Ring_Exception("__set is disabled");
    }
    
    /**
     *    
     */
    public function __unset($name)
    {
        require_once 'Mic/Ring/Exception.php';
        throw new Mic_Ring_Exception("__unset is disabled");
    }
    
    /**
     * Prepends a value or set of values to the stack
     * 
     * @param mixed $args
     */
    public function unshift()
    {
        $this->_put(func_get_args(), false); 
        $free = $this->_getFree();
        
        if ($free < 0) {
            array_splice($this->_data, $free, abs($free));
        }
        
        return $this;
    }
    
    /**
     * Appends a value or set of values to the stack
     * 
     * @param mixed $args
     */
    public function push()
    {
        $this->_put(func_get_args(), true);
        $free = $this->_getFree();
        
        if ($free < 0) {
            array_splice($this->_data, 0, abs($free));
        }
        
        return $this;
    }
    
    /**
     * Pushes or unshifts a value or set of values onto the stack
     * 
     * Supports sets in these formats:
     *  - scalar:  10
     *  - array:   array(10, 11, 12)
     *  - literal: 10, 11, 12
     *  - mixed:   array(10, 11, 12), 13, 14, array(array(15, 16, 17))
     * 
     * @param mixed $set
     * @param boolean $push
     * @return mixed
     */
    private function _put(array $set=array(), $push=true)
    {
        foreach($set as $item) {
            if (is_array($item)) {
                call_user_func(array($this, '_put'), $item, $push);
            } else {
                if ($push) {
                    array_push($this->_data, $item);
                } else {
                    array_unshift($this->_data, $item);
                }
            }
        }
    }
    
    /**
     * Calculates remaining slots in buffer
     *
     * @return integer
     */
    private function _getFree()
    {
        return $this->_buffer - $this->count();   
    }
}
