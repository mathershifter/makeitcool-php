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
 * @package   MC_Object
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @filesource
 */

/**
 * MC_Object
 *
 * @category  MC
 * @package   MC_Object
 */
abstract class MC_Object
{
    /**
     * Needs to be overriden to do anything
     *
     * @param $method
     * @param $params
     * @return mixed
     */
    public function __call($method, $params=array())
    {
        throw new Exception("Undefined method: $method");
    }
    
    /**
     * Call an available method
     */
    public function call($method, array $params=array())
    {
        return call_user_func_array(array($this, $method), $params);
    }
    
    /**
     * Check if object can perform a method
     *
     * @todo call and send are very similar.  The arguments are handled differently.  Which one to keep???
     * @param string $method
     * @return boolean
     */
    public function can($method)
    {
        return method_exists($this, $method) ? true : false;
    }
        
    /**
     * Gets class of object
     *
     * @return string class name
     */
    public function instance()
    {
        return get_class($this);
    }
    
    /**
     * Gets class name of object or tests if object is an instance of a class
     *
     * @param string $class
     * @return string|boolean
     */
    public function isA($class=null)
    {
        if ($class) {
            return ($this instanceof $class) ? true : false;
        } else {
            return get_class($this);
        }
    }
    
    /**
     * Converts object to a JSON formatted string
     *
     * @return string
     */
    public function jsonize()
    {
        return json_encode($this);
    }
    
    /**
     * Gets parent class name for this object
     *
     * @return string parent class name
     */
    public function kindOf()
    {
        $parent = get_parent_class($this);
        return $parent ? $parent : $this->instance();    
    }
    
    /**
     * Backward compatible version of kindOf
     */
    public function kind() { return $this->kindOf(); }
    
    /**
     * Exports an md5 hash of the data
     *
     * @return string
     */
    public function md5()
    {
        return md5($this->serialize());   
    }
    
    /**
     * Call a method available to the current object
     */
    public function send($method)
    {
        $args = func_get_args();
        return call_user_func_array(array($this, $args[0]), array_splice($args, 1));
    }
    
    /**
     * Converts object to a PHP serialized string
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this);
    }
}
