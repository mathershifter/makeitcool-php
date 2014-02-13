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
 * @package   Mic_Cacti
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_Cacti_ScriptServer_Plugin_Mpstat
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer_Plugin_Cpu extends Mic_Cacti_ScriptServer_Plugin
{
    private $_pluginClass;
    /**
     *
     */
    protected function _init($params)
    {
        $format = isset($params['format']) && $params['format'] != '' ? $params['format'] : 'linux';
        
        $this->_pluginClass = __CLASS__ . '_' . ucfirst(strtolower($format));
        
        if (method_exists($this->_pluginClass, 'getParser')) {
            $this->_setParser(call_user_func(array($this->_pluginClass, 'getParser')));
        }
    }
    
    /**
     * 
     */
    protected function _execute($params=array())
    {
        $result = call_user_func(array($this->_pluginClass, 'execute'), $params);
        
        return $result;
    }
    
    /**
     * 
     */
    protected function _parse($result)
    {
        try {
            $result = call_user_func(array($this->_pluginClass, 'parse'), $result);
        } catch (Mic_Cacti_ScriptServer_Plugin_Cpu_Exception $e) {
            $result = parent::_parse($result);
        }
        
        return $result;
    }
}