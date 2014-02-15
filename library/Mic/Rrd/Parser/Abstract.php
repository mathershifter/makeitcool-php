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
 * @version   SVN: $Id: Abstract.php 801 2010-03-24 22:55:35Z jmather5 $
 * @filesource
 */

/**
 * MC_Rrd_Parser_Abstract
 *
 * @category MC
 * @package  MC_Rrd
 */
abstract class MC_Rrd_Parser_Abstract
{
    /**
     * 
     * @param mixed $response
     * @return mixed
     */
    public static function parse($response)
    {
        return $response;
    }
}
