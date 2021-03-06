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
 * MC_Cacti_ScriptServer_Parser_Mpstat_Cpu_Base
 *
 * @category  MC
 * @package   MC_Cacti
 */
abstract class MC_Cacti_ScriptServer_Plugin_Cpu_Base
{
    abstract public static function execute($params);
 
    public static function parse($result)
    {
        throw new MC_Cacti_ScriptServer_Plugin_Cpu_Exception("Parse method not implemented.");
    }
       
    public static function getParser()
    {
        return null;
    }
}