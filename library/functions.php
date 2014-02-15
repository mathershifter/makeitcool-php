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
 * @package   MC
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: Rrd.php 568 2010-02-09 03:47:40Z jmather5 $
 * @filesource
 */

/**
 * Wrapper for creating a base object
 *
 * @return MC_Object
 */
function O()
{
    return new MC_Object();
}

/**
 * Wrapper for creating an array object
 *
 * @param array $array
 * @return MC_Array
 */
function A($array=array())
{
    return new MC_Array($array);
}

function C($array=array())
{
    return new MC_Collection($array);
}

/**
 * Wrapper for creating a string object
 *
 * @param string $string
 * @return MC_String
 */
function S($string='')
{
    return new MC_String($string);
}
