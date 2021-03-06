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
 * @package   MC_Array
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @deprecated
 * @filesource
 */

/**
 * Zend_Console_Getopt
 */
require_once 'Zend/Console/Getopt.php';

trigger_error(basename(__FILE__) . " is deprecated", E_USER_WARNING);

/**
 * MC_Consol_Getopt
 *
 * @category  MC
 * @package   MC_Console
 */
class MC_Console_Getopt extends Zend_Console_Getopt
{
    /**
     * 
     */
    public function toObject($class='stdClass')
    {
        $object = new $class();
        foreach ($this->_options as $key=>$val) {
            $object->{$key} = $val;
        }
        
        return $object;
    }
}