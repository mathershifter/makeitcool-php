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
 * Mic_Cacti_ScriptServer_Parser_Mpstat
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer_Parser_Mpstat extends Mic_Cacti_ScriptServer_Parser
{
    /**
     * 
     */
    public static function parse($data)
    {
        # loop through the parsers until one is successful
        foreach (scandir(dirname(__FILE__) . '/Mpstat') as $file) {
            
            $file = dirname(__FILE__) . '/Mpstat/' . $file;
            
            if (S($file)->rmatch('/.php$/')) {
                
                require_once $file;
                
                $klass = __CLASS__ . '_' . basename($file, '.php');
                
                if ($parsed = call_user_func("$klass::parse", $data)) {
                    //print_r($parsed);
                    return $parsed;
                }
            }
        }
    }
}