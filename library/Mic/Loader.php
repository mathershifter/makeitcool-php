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
 * @package   Mic_Loader
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_Loader
 *
 * @category  Mic
 * @package   Mic_Loader
 */
class Mic_Loader
{
    /**
     * Load a class file
     * 
     * @param string $class      - The full class name of a component.
     * @param boolean $quiet
     * @return void
     * @throws Mic_Exception
     */
    public static function loadClass($class, $quiet=false)
    {
        // look no further if class exists
        if (class_exists($class)) return;
        
        $path = preg_replace('/_/', DIRECTORY_SEPARATOR, $class) . '.php';
        
        $errorReporting = error_reporting();
        
        error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_USER_ERROR
            | E_USER_WARNING | E_USER_NOTICE | E_USER_DEPRECATED);
        
        // try and load the class file
        if ((include_once $path) === false) {
            if (!$quiet) {
                error_reporting($errorReporting);
                require_once('Mic/Loader/Exception.php');
                throw new Mic_Loader_Exception("{$path} was not readable");
            }
        } elseif (!class_exists($class)) {
        
            // if class is still not defined explain how thing should work
            if (!$quiet) {
                error_reporting($errorReporting);
                require_once('Mic/Loader/Exception.php');
                throw new Mic_Loader_Exception("Expected {$path} to define {$class}");
            }
        
        }
        
        error_reporting($errorReporting);
    }
    
    public static function autoload($class)
    {
        self::loadClass($class, true);
    }
}
