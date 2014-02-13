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
 * Mic_Cacti_ScriptServer_Parser_Mpstat_Cpu_Base
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
abstract class Mic_Cacti_ScriptServer_Plugin_Cpu_Base
{
    abstract public static function execute($params);
 
    public static function parse($result)
    {
        throw new Mic_Cacti_ScriptServer_Plugin_Cpu_Exception("Parse method not implemented.");
    }
       
    public static function getParser()
    {
        return null;
    }
}