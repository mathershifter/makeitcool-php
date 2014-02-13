<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Make It Cool (Mic) PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: Rrd.php 568 2010-02-09 03:47:40Z jmather5 $
 * @filesource
 */

/**
 * Mic
 * 
 * @category   Mic
 * @package    Mic
 * @author     Jesse R. Mather <jrmather@gmail.com>
 * @copyright  2009 Nobody
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version    Release: @package_version@
 */
class Mic
{
    /**
     * Sets the include path, registers the autoloader, and loads convenience
     * functions
     *
     * @return void
     */
    public static function boot()
    {
        // this file is in the base of the library path
        $libraryPath = dirname(__FILE__);
        
        // get php include path
        $includePath = get_include_path();
        
        // if the library path is not already in the include path, add it
        if (!strstr($includePath, $libraryPath)) {
            set_include_path($libraryPath . PATH_SEPARATOR . $includePath);
        }
        
        // register the auto-loader
        require_once('Mic/Loader.php');
        spl_autoload_register(array('Mic_Loader', 'autoload'), true);
        
        // load wrapper functions
        require_once 'functions.php';
    }
}