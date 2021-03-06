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
 * @package   MC_Rrd
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @filesource
 */

/**
 * @see MC_Rrd_Parser_Abstract
 */
require_once 'MC/Rrd/Parser/Abstract.php';

/**
 * MC_Rrd_Parser_Pwd
 *
 * Class for parsing rrdtool pwd response
 *
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Parser_Pwd extends MC_Rrd_Parser_Abstract
{
    public static function parse($response) 
    {
        return is_array($response) ? array_shift($response) : $response;
    }
}
