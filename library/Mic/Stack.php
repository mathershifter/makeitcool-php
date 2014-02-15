<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Stack
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @deprecated
 * @filesource
 */

/**
 * @see MC_Array
 */
require_once 'MC/Array.php';

trigger_error(__FILE__ . " is deprecated", E_USER_WARNING);

/**
 * MC_Stack
 * 
 * @category  MC
 * @package   MC_Stack
 */
class MC_Stack extends MC_Array
{
    /**
     * 
     * @param mixed $data
     */
    protected function _init($args)
    {
        $data = !empty($args) ? array_shift($args) : array();
        
        return is_array($data) ? $data : array();
    }
}
