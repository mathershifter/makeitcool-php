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
 * @package   MC_Template
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @deprecated
 * @filesource
 */
 
trigger_error(basename(__FILE__) . " is deprecated", E_USER_WARNING);

/**
 * @see MC_Object
 */
require_once 'MC/String.php';

/**
 * MC_Template
 *
 * @category  MC
 * @package   MC_Template
 */
class MC_Template extends MC_String
{
    
    /**
     * 
     */
    //public function __construct($template)
    protected function _init($args)
    {
        $template = array_shift($args);
        if (file_exists($template)) {
            if (!is_readable($template)) {
                //
                require_once 'MC/Template/Exception.php';
                throw new MC_Template_Exception("Failed to read file: $file");
            }
            
            $template = file_get_contents($template);
        }
        
        return $template;
    }
    
    /**
     * 
     */
    private function _readFile($file)
    {
        ob_start();
        include($file);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    
    /**
     * 
     */
    public function replace($tag, $replacement=null, $limit=-1, &$count=null)
    {
        if (!is_array($tag) && !$tag instanceof MC_Array) {
            return parent::replace($tag, $replacement, $limit, $count);
        }
        
        if (!empty($tag)) {
            foreach ($tag as $key => $value) {
                $key = addcslashes($key, "|:.+*?[^]($)<>-");
                // replacement string. can be an external file.
                $_value  = (is_string($value) && file_exists($value) && is_readable($value))
                    ? $this->_readFile($value)
                    : $value;
                
                $this->_data = preg_replace(
                    "/\{$key\}/", $_value, $this->_data, -1, $count);
            }
        }
        
        return $this; // new parent($this);
    }
    
    /**
     * 
     */
    public function flush()
    {
        echo $this->_data;
        $this->_data = null;
    }
    
    /**
     * 
     */
    public static function process($template, $tags)
    {
        $_tmp = new self($template);
        return new parent($_tmp->replace($tags));
    }
}