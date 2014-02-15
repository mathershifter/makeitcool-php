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
 * @version   SVN: $Id: Fetch.php 801 2010-03-24 22:55:35Z jmather5 $
 * @filesource
 */

/**
 * @see MC_Rrd_Parser_Abstract
 */
require_once 'MC/Rrd/Parser/Abstract.php';

/**
 * MC_Rrd_Parser_Fetch
 *
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Parser_Fetch extends MC_Rrd_Parser_Abstract
{
    public static function parse($response) 
    {
        $data = array();
        
        $keys = array('timestamp');
        
        foreach ($response as $line) {
            $line = trim($line);
            
            // the first non-empty line should be the ds names
            if (count($keys) == 1 && preg_match('/^\w+/', $line)) {
                $keys = array_merge($keys, preg_split('/\s+/', $line));
            } elseif (preg_match('/^([0-9]+)\:\s+([0-9].*)/', $line, $matches)) {
                $_record = array();    
                
                // First match is the timestamp
                $values  = array((int) $matches[1]);
                
                $_sv = preg_split('/\s+/', trim($matches[2]));
                
                // convert from scientific notation
                foreach ($_sv as $sn) {
                    array_push($values, (float) $sn);
                }
                
                if (($_record = @array_combine($keys, $values)) === false) {
                    require_once 'MC/Rrd/Parser/Exception.php';
                    throw new MC_Rrd_Parser_Exception("Parser error: field count does not match value count");
                }
                
                $data[] = $_record;
            }
        }
        
        return $data;
    }
}
