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
 * Mic_Cacti_ScriptServer
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer
{
    /**
     * Call the plugins
     */
    public static function call($plugin, $params)
    {
        if (!$plugin) {
            throw new Mic_Cacti_ScriptServer_Exception("No plugin specified");
        }
        
        $params = self::_parseParams($params);
        
        $klass = __CLASS__ . '_Plugin_' . S($plugin)->classify();
        
        if (!class_exists($klass)) {
            throw new Mic_Cacti_ScriptServer_Exception("No plugin defined for '$plugin'.");
        }
        
        $plugin = new $klass($params);
        
        return $plugin->call($params);
    }
    
    /**
     * 
     */
    private static function _parseParams($params)
    {    
        $_params = A();
        foreach ($params as $_pair) {
            list($key, $val) = explode(':', $_pair);    
            $_params[$key] = $val ? $val : null;
        }
        
        return $_params;
    }
}