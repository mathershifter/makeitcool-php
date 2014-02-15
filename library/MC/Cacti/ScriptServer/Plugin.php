<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Cacti
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Cacti_ScriptServer_Plugin
 *
 * @category  MC
 * @package   MC_Cacti
 */
abstract class MC_Cacti_ScriptServer_Plugin
{
    /**
     * 
     */
    private $_data     = array(
        
    );
    
    private $_paserClassPrefix = 'MC_Cacti_ScriptServer_Parser_';
    
    /**
     * 
     */
    protected $_parser = null;
    
    /**
     * 
     */
    public function __construct($params=array())
    {
        $this->_init($params);        
    }
    
    /**
     * 
     */
    public function __get($name)
    {
        return isset($this->_data[$name]) ? $this->_data[$name] : null;
    }
    
    /**
     * 
     */
    protected function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }
    
    
    /**
     * 
     */
    public function __toString()
    {
        if (!$this->result) {
            return '';
        }
        
        $_tmp = new MC_Array();
        foreach ($this->result as $key=>$value) {
            $_tmp->push("{$key}:{$value}");
        }
        
        return "{$_tmp->join(' ')}";
    }
    
    /**
     * 
     */
    abstract protected function _execute($params=array());
    
    
    /**
     * 
     */
    protected function _init($params) {}
    
    
    protected function _getParser()
    {
        return $this->_parser;
    }
    
    /**
     * 
     */
    protected function _parse($result=array())
    {
        //echo "PARSING...{$this->_parser}\n";   
        if ($this->_parser) {
            $result = call_user_func("{$this->_parser}::parse", $result);
        }
        
        return $result;
        
    }
    
    protected function _setParser($name)
    {
        if (!$name) return;
        
        $parser    = S($name)->classify();
        $absParser = "{$this->_paserClassPrefix}{$parser}";
        
        if (class_exists($parser)) {
        } elseif (class_exists($absParser)) {
            $parser = $absParser;
        } else {
            throw new MC_Cacti_ScriptServer_Plugin_Exception(
                "Parsers '{$parser}' or '{$absParser}' do not exist");
        }
        
        $this->_parser = $parser;
    }
    
    
    /**
     * 
     */
    public function call($params=array())
    {
        try {        
            $this->result = $this->_parse($this->_execute($params));
        } catch (MC_Cacti_ScriptServer_Exception $e) {
            // issue warning or log?
        }
        
        return $this;
    }
    
}