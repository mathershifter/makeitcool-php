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
 * @package   Mic_String
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see Mic_Object
 */
require_once 'Mic/Object.php';

/**
 * Class for a string object
 * 
 * @category  Mic
 * @package   Mic_String
 */
class Mic_String extends Mic_Object
{
    /**
     * Stores the actual data
     *
     * @var mixed
     */
    protected $_data;
    
    /**
     * @param mixed $data
     */
    public function __construct()
    {
        $args = func_get_args();
        
        $string = $this->_init($args);
        
        $this->_data = (string) $string;
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toS();
    }

    /**
     * Initializes the string
     *
     * @param mixed $args
     * @return string
     */
    protected function _init($args)
    {
        return !empty($args) ? array_shift($args) : '';
    }
    
    /**
     * Converts string to camel case
     *
     * @return DataType_String
     */
    public function camelize()
    {
        return $this->trim()->underscorize()
           ->replace('~(_?)(_)([\w])~e', '"$1".strtoupper("$3")');
    }
    
    /**
     * Converts string to camel case, with leading character capitalized
     *
     * @return DataType_String
     */
    public function classify()
    {
        return $this->camelize()->capitalize();
    }
    
    /**
     * Capitalizes the first character in the string 
     *
     * @return DataType_String
     */
    public function capitalize()
    {
        return new self(ucfirst($this->_data));
    }
    
    /**
     * What is this?
     */
    public function create($data)
    {
        return new self($data);
    }
    
    /**
     * Converts the string to hex
     * 
     * @return DataType_String
     */
    public function hex()
    {
        $hex    = false;
        
        for ($i=0; $i < strlen($this->_data); $i++) {
            $hex .= dechex(ord($this->_data[$i]));
        }
        
        return new self($hex);
    }
    
    /**
     * 
     * @param string $pattern
     * @param boolean $all
     * @return array|boolean
     */
    public function grep($pattern, $all=false)
    {
        $matches = array();
        $matched = $all ? preg_match_all($pattern, $this->_data, $matches) : preg_match($pattern, $this->_data, $matches);
        
        return $matched > 0 
            ? (count($matches) == 1 ? new self($matches[0]) : new Mic_Array($matches))
            : false;
    }
    
    /**
     * Hashes a string 
     *
     * @param string algorithm
     */
    public function hash($algorithm='md5')
    {
        return hash($algorithm, $this->_data);
    }
    
    /**
     * Creates a human formatted version of the string
     *
     * @return Mic_String
     */
    public function humanize()
    {
        return $this->trim()->underscorize()
            ->replace('~(_?)(_)([\w])~e', '"$1 ".strtoupper("$3")')
            ->capitalize();
    }
    
    /**
     * Checks whether the string is hex
     *
     * @return boolean
     */
    public function ishex()
    {
        return $this->unhex()->hex()->toS() === $this->_data ? true : false;
    }
    
    public function len()
    {
        return strlen($this->_data);
    }
    
    /**
     * Converts the whole string to lower case letters 
     *
     * @return DataType_String
     */
    public function lower()
    {
        return new self(strtolower($this->_data));
    }
    
    /**
     * 
     */
    public function match($string, $before_needle=false)
    {
        $match = version_compare(PHP_VERSION, '5.3.0') >= 0
            ? strstr($this->_data, $string, $before_needle)
            : strstr($this->_data, $string);
        
        return $match ? new self($match) : false;
    }
    
    public function md5()
    {
        return md5($this->_data);
    }
    
    /**
     * Substitues pattern with replacement string
     * 
     * @param  $pattern
     * @param  $replacement
     * @return DataType_String
     */
    public function replace($pattern, $replacement=null, $limit=-1, &$count=null)
    {
        return new self(preg_replace($pattern, $replacement, $this->_data, $limit, $count));
    }
    
    /**
     * 
     */
    public function rmatch($pattern)
    {
        return $this->grep($pattern);
    }
    
    /**
     * 
     */
    public function rmatchAll($pattern)
    {
        return $this->grep($pattern, 1);
    }
    
    /**
     * 
     */
    public function rsplit($pattern, $limit=null, $flags=null)
    {
        return new Mic_Array(preg_split($pattern, $this->_data, $limit, $flags));
    }
    
    /**
     * 
     */
    public function slice($offset=0, $length=null)
    {
        
        return new self(is_int($length) 
            ? substr($this->_data, $offset, $length)
            : substr($this->_data, $offset)
        );
    }
    
    public function splice($offset=0, $length=null)
    {
        $capture = $this->slice($offset, $length);
        
        $this->_data = substr_replace($this->_data, '', $offset, $length);
        
        return $capture;
    }
    
    /**
     * 
     */
    public function split($delimeter='')
    {
        return new Mic_Array($delimeter ? explode($delimeter, "{$this->_data}") : str_split("{$this->_data}"));
    }
    
    /**
     * 
     */
    public function substr($offset=0, $length=null)
    {
        return $this->slice($offset, $length);
    }
    
    /**
     * @return string
     */
    public function toS()
    {
        return (string) $this->_data;
    }
    
    /**
     * 
     */
    public function trim()
    {
        return new self(trim($this->_data));        
    }
    
    public function trunc($length, $suffix = '...')
    {
        return $this->len() > $length ? $this->slice(0, $length) . $suffix : $this;
    }
    
    /**
     * Converts non-word characters to underscores and lower cases the string
     *
     * @return DataType_String
     */
    public function underscorize()
    {
        $tmp = $this->grep('/[a-z]/')
            ? $this->replace('~(?<=\\w)([A-Z])~', '_$1') : $this;
        
        return $tmp->replace('/\W+/', '_')->lower();
    }
    
    /**
     * Converts the string from hex
     * 
     * @return DataType_String
     */
    public function unhex()
    {
        $string = false;
        
        for ($i=0; $i < strlen($this->_data)-1; $i+=2){
            $string .= chr(hexdec($this->_data[$i] . $this->_data[$i+1]));
        }
        
        return new self($string);
    }
    
    /**
     * Converts the whole string to upper case letters
     *
     * @return DataType_String
     */
    public function upper()
    {
        return new self(strtoupper($this->_data));
    }
}
