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
 * @package   Mic_Stack
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
 * Mic_Stack
 * 
 * @category  Mic
 * @package   Mic_Stack
 */
class Mic_Stack extends Mic_Array
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
