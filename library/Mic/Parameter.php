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
 * @package   Mic_Parameter
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @deprecated
 * @filesource
 */

/**
 * @see Mic_Array
 */
require_once 'Mic/Array.php';

trigger_error(__FILE__ . " is deprecated", E_USER_WARNING);

/**
 * Base class for time objects
 *
 * @category  Mic
 * @package   Mic_Parameter
 */
class Mic_Parameter extends Mic_Array
{
    /**
     * 
     */
    private static $_classMap = array(
        '/^cli$/'        => 'Cli',
        '/apache|f?cgi/' => 'Http',
    );
    
    /**
     * 
     */
    public static function map()
    {
        foreach (A(self::$_classMap) as $pattern=>$klass) {
            if (S(PHP_SAPI)->rmatch($pattern)) {
                $klass = __CLASS__ . '_' . S($klass)->capitalize();
                return call_user_func("{$klass}::map");
            }
        }
    }
}