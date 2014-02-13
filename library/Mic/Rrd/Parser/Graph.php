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
 * @version   SVN: $Id: Graph.php 1095 2010-06-22 00:10:57Z jmather5 $
 * @filesource
 */

/**
 * @see Mic_Rrd_Parser_Abstract
 */
require_once 'Mic/Rrd/Parser/Abstract.php';

/**
 * Mic_Rrd_Parser_Graph
 *
 * XXX
 *
 * @category Mic
 * @package  Mic_Rrd
 */
class Mic_Rrd_Parser_Graph extends Mic_Rrd_Parser_Abstract
{
    /**
     * 
     * @param mixed $response
     * @return mixed
     */
    public static function parse($response)
    {
        if (is_array($response)) {
            $data = array();
            array_shift($response);
            foreach ($response as $line) {
                // split on the ':' if there is one
                if (preg_match('/(\w+)\s*\:\s*(.*)/', $line, $matches)) {
                    $data[$matches[1]] = $matches[2];
                } else {
                    array_push($data, $line);
                }
            }
            return $data;
        }
        
        return $response;
    }
}
