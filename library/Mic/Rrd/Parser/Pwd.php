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
 * @package   Mic_Rrd
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: Pwd.php 801 2010-03-24 22:55:35Z jmather5 $
 * @filesource
 */

/**
 * @see Mic_Rrd_Parser_Abstract
 */
require_once 'Mic/Rrd/Parser/Abstract.php';

/**
 * Mic_Rrd_Parser_Pwd
 *
 * Class for parsing rrdtool pwd response
 *
 * @category   Mic
 * @package    Mic_Rrd
 */
class Mic_Rrd_Parser_Pwd extends Mic_Rrd_Parser_Abstract
{
    public static function parse($response) 
    {
        return is_array($response) ? array_shift($response) : $response;
    }
}
