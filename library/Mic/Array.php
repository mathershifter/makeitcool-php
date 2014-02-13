<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Array
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see Mic_Object
 */
require_once 'Mic/Object.php';

/**
 * Mic_Array
 *
 * @category  Mic
 * @package   Mic_Array
 */
class Mic_Array extends Mic_Object implements Countable, Iterator, ArrayAccess
{
    /**
     * @var array
     */
    protected $_data = array();
    
    /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index = 0;
    
    /**
     * Used when unsetting values during iteration to ensure we do not skip
     * the next element
     *
     * @var boolean
     */
    protected $_skipNextIteration;
    

    /**
     * 
     * @param mixed $data
     */
    public function __construct()
    {
        $args = func_get_args();
        
        /**
         * Allow overriding class to process args
         */
        $data = $this->_init($args);
        
        // build the array recusively creating Mic_Array objects
        foreach ($data as $key=>$value) {
            $this->offsetSet($key, $value);
        }
        
        // allow extending classes alter data after assignment
        $this->_postInit();
    }
    
    /**
     * Retrieves an element from the collection by key 
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
        
    /**
     * Support isset() overloading
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }
    
    /**
     * Adds or replaces an element in the collection
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
    
    /**
     * 
     * @return unknown_type
     */
    public function __toString()
    {
        return $this->jsonize();
    }

    /**
     * Support unset() overloading
     *
     * @param string $name
     * @return mixed
     */
    public function __unset($name)
    {
        if ($this->__isset($name)) {
            unset($this->_data[$name]);
            $this->_skipNextIteration = true;
        }
    }
    
    /**
     * Does nothing unless overridden 
     *
     * @param mixed $data
     * @return mixed
     */
    protected function _init($args)
    {
        return is_array($args) && !empty($args) ? array_shift($args) : (array) $args;
    }
    
    /**
     * 
     */
    protected function _postInit()
	{
		//...
	}
    
    /**
     * @return array
     */
    public function cast()
    {
        return $this->toArray();
    }
    
    /**
     * Find a value in an array
     */
    public function contains($value, $strict=true)
    {
        //print_r($this);
        return in_array($value, $this->_data, $strict);        
    }
    
    /**
     * 
     */
    public static function convert($data) {
        
        if (is_object($data)) {
            if ($data instanceof Mic_Array or is_subclass_of($data, 'Mic_Array')) {
                return $data;  
            }
            return new Mic_Array((array) $data);
        } elseif (is_array($data)) {
            return new Mic_Array($data);
        } else {
            return $data;
        }
    }
    
    /**
     * Defined by Countable interface
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_data);
    }
	
    /**
     * Statically creates a new array object
     *
     * @param array $data
     * @return Mic_Array
     */
    public static function create($data=array())
    {
        return new self($data);
    }
    
    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        $this->_skipNextIteration = false;
        return current($this->_data);
    }
    
	/**
	 * 
	 */
    public function deleteAt($key)
    {
        $deleted = $this->_data[$key];
        unset($this->_data[$key]);
        
        return $deleted;
    }
    
    /**
     * Defined by Iterator interface
     * 
     * @return mixed
     */
    public function end()
    {
        return end($this->_data);
    }
    
    /**
     * 
     */
    public static function fill($start, $count, $value=null)
    {
        return new self(array_fill($start, $count, $value));
    }
    
    /**
     * 
     */
    public function fillKeys($value=null)
    {
        return new self(array_fill_keys(array_keys($this->_data), $value));
    }
    
    /**
     * Gets first value from array
     *
     * @return mixed
     */
    public function first()
    {
        return $this->values()->shift();
    }
    
    /**
     * 
     */
    public function flip()
    {
        $class = $this->instance();
        return new $class(array_flip($this->_data));
    }
    
    /**
     * Retrieves an element from the collection by key 
     *
     * @param string $name
     * @return mixed
     */
    public function get($name=false)
    {
        
        if (is_object($name) && method_exists($name, '__toString')) {
            $name = $name->__toString();
        }
        
        if ($name === false || $name === null) {
            return $this->current();
        }
        
        
        return array_key_exists($name, $this->_data) ?
           $this->_data[$name] : null;
    }
    
    /**
     * 
     */
    public function hasKey($name)
    {
        return array_key_exists($name, $this->_data) ? true : false;
    }
    
    /**
     * 
     */
    public function isEmpty()
    {
        return empty($this->_data) ? true : false;
    }
    
    /**
     * 
     */
    public function insert($offset, $data, $overwrite=false)
    {
        if (is_scalar($data)) {
            $data = new self(array($data));
        } elseif (is_array($data)) {
            $data = new self($data);
        } elseif (!$data instanceof Mic_Array) {
            throw new Mic_Array_Exception("Data must be an array, scalar, or Mic_Array.");            
        }
        
        if ($overwrite) {
            $this->deleteAt($offset);
        }
        
        $left  = $this->values()->slice(0, $offset);        
        $right = $this->values()->slice($offset);
        
        $this->_data =  $left->merge($data->values())->merge($right)->toArray();
        
    }
    
    /**
     * Joins array values
     * 
     * @param string $delim
     * @return string
     */
    public function join($delim='')
    {
        return join($delim, $this->_data);                
    }
    
    /**
     * JSON ecodes data
     *
     * return string
     */
    public function jsonize()
    {
        return json_encode($this->toArray());
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }
    
    /**
     * 
     */
    public function keys()
    {
        return new self(array_keys($this->_data));
    }
    
    /**
     * 
     */
    public function ksort($arg=SORT_REGULAR)
    {
        if (is_callable($arg)) {
            uksort($this->_data, $arg);
        } else {
            ksort($this->_data, $arg);
        }
        return $this;
    }
    
    /**
     * Alias for end
     *
     * @return mixed
     */
    public function last()
    {
        return $this->end();
    }
    
    /**
     * 
     */
    public function length()
    {
        return $this->size();
    }
    
    public function map($callback)
    {
        $class = $this->instance();
        return new $class(array_map($callback, $this->_data));
    }
    
    /**
     * Merges an array into the array
     *
     * @param array $array
     * @return Mic_Array
     */
    public function merge($array=array())
    {
        $class = $this->instance();
        return new $class(array_merge($this->_data, self::convert($array)->toArray()));
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function next()
    {
        if ($this->_skipNextIteration) {
            $this->_skipNextIteration = false;
            return;
        }
        next($this->_data);
        $this->_index++;
    }
    
    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }
    
    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    
    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    
    /**
     * Defined by ArrayAccess interface
     *
     * @param mixed $offset
     * @return boolean
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
    
    /**
     * Pops one or more values from the stack
     * 
     * @throws Mic_Array_Exception
     * @param integer $count
     * @return mixed
     */
    public function pop()
    {
        if (empty($this->_data)) {
            require_once 'Mic/Array/Exception.php';
            throw new Mic_Array_Exception("Not enough elements for this " .
                                          "operation");
        }
        
        return array_pop($this->_data);
    }
    
    /**
     *
     */
    public function push($value)
    {
        $class = $this->instance();
        return array_push($this->_data,
            is_array($value) ? new $class($value) : $value);
    }

    /**
     * Defined by Iterator interface
     *
     * @return void
     */
    public function rewind()
    {
        $this->_skipNextIteration = false;
        reset($this->_data);
        $this->_index = 0; 
    }
    
    /**
     * Reverse the collection. 
     *
     * @param boolean $preserveKeys
     * @return Mic_Array
     */
    public function reverse($preserveKeys=false)
    {
        $class = $this->instance();
        return new $class(array_reverse($this->cast(), $preserveKeys));
    }
    
    /**
     * 
     */
    public function rotate()
    {
        $columns = array();

        foreach ($this->_data as $index=>$row)
        {
            if (!$row instanceof Mic_Array) {
                require_once('Mic/Array/Exception.php');
                throw new Mic_Array_Exception("Value at index '{$index}' " .
                	                          "must be an array");
            }
            foreach ($row as $key=>$value)
            {
                $columns[$key][$index] = $value;            
            }
        }

        return new self($columns);
    }
    
    /**
     * 
     */
    public function search($value, $strict=true)
    {
        return array_search($value, $this->_data, $strict);
    }
    
    /**
     * Adds or replaces an element in the collection
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value)
    {
        $class = $this->instance();
        $this->_data[$name] = is_array($value) ? new $class($value) : $value;
    }
    
    /**
     * Pops one or more values from the stack
     * 
     * @param integer $count
     * @return mixed
     */
    public function shift()
    {
        return !empty($this->_data) ? array_shift($this->_data) : false;
    }
    
    /**
     * 
     */
    public function size()
    {
        return count($this->_data);
    }
    
    /**
     * Wrapper for php array_slice function
     *
     * @param integer $offset
     * @param integer $length
     * @param mixed $replacement
     * @return Mic_Array
     */
    public function slice($offset, $length=null, $preserveKeys=false)
    {
        $class  = $this->instance();
        
        $length = is_numeric($length) ? $length : $this->count() - $offset;
        
        return new $class(array_slice($this->_data, $offset, $length,
           $preserveKeys));
    }
    
    /**
     * Wrapper for php array_splice function
     *
     * @param integer $offset
     * @param integer $length
     * @param mixed $replacement
     * @return Mic_Array
     */
    public function splice($offset, $length=null, $replacement=null)
    {
        $class  = $this->instance();
        
        $length = is_numeric($length) ? $length : $this->count() - $offset;
        
        return new $class(array_splice($this->_data, $offset, $length,
           $replacement));
    }
    
    /**
     * Sorts the array using callback
     *
     * @param mixed $arg callback or sort flag
     * @return Mic_Array
     */
    public function sort($arg=SORT_REGULAR)
    {
        if (is_callable($arg)) {
            usort($this->_data, $arg);
        } else {
            sort($this->_data, $arg);
        }
        return $this;
    }
    
    /**
     * Return an array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $_data = array();
        
        foreach ($this->_data as $key=>$val) {
            $_data[$key] = method_exists($val, 'toArray') ? $val->toArray() : $val;
        }
        
        return $_data;
    }
    
    public function toA()
    {
        return $this->toArray();
    }
    
    /**
     * 
     */
    public function uniq() { return $this->unique(); }
    
    /**
     * 
     */
    public function unique()
    {
        return new self(array_unique($this->_data));
    }
    
    /**
     * Sorts the array using callback
     *
     * @param mixed $arg callback function
     * @return Mic_Array
     */
    public function usort($callback)
    {
        if (!is_callable($callback)) {
        	require_once 'Mic/Array/Exception.php';
            throw new Mic_Array_Exception("Expected '$callback' to be " .
            	                          "callable.");
        }
        
        usort($this->_data, $callback);
        
        return $this;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->count();
    }
    
    /**
     * 
     */
    public function values()
    {
        return new self(array_values($this->_data));              
    }
        
    /**
     * XXX 
     *
     * @param mixed $value
     * @return mixed
     */
    public function unshift($value)
    {
        array_unshift($this->_data,
            is_array($value) ? new Mic_Array($value) : $value);
    }
    
    /**
     * XXX
     *
     * @param array $values
     * @return Mic_Array
     */
    public function zip($values=array())
    {
        $values = self::convert($values);
        $keys   = $this->values();
        
        if ($keys->count() !== $values->count()) {
            throw new Mic_Array_Exception("Number of elements in keys and values
                do not match");       
        }
        
        return new self(array_combine($keys->toArray(), $values->toArray()));  
    }
}
