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
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see Mic_Rrd_Parser_Abstract
 */
require_once 'Mic/Rrd/Parser/Abstract.php';

/**
 * Mic_Rrd_Parser_Info
 *
 * Parses rrdtool info output
 *
 * @category   Mic
 * @package    Mic_Rrd
 */
class Mic_Rrd_Parser_Info extends Mic_Rrd_Parser_Abstract
{
    const REG_MATCH_BASE = '/^(\w+)\s+=\s+\"?([\w\+\.\/\-]+)\"?$/';
    const REG_MATCH_DS   = '/^ds\[(\w+)\]\.(\w+)\s+=\s+\"?([[:alnum:]\+\-\._]+)\"?$/';
    const REG_MATCH_RRA  = '/^rra\[(\d+)\]\.(\w+)(?:\[(\d+)\])?(?:\.(\w+))?\s+=\s+\"?([[:alnum:]\+\-\._]+)\"?$/';
    
    /**
     * 
     * @param mixed $response
     * @return mixed
     */
    public static function parse($response)
    {
        $info = array();
        foreach ($response as $line) {
            
            if (preg_match(self::REG_MATCH_BASE, $line, $matches)) {
                $info[$matches[1]] = self::_setType($matches[2]);
            } elseif (preg_match(self::REG_MATCH_DS, $line, $matches)) {
                $info['ds'][$matches[1]][$matches[2]] = self::_setType($matches[3]);
            } elseif (preg_match(self::REG_MATCH_RRA, $line, $matches)) {
                if ($matches[3] === '') {
                    $info['rra'][(int) $matches[1]][$matches[2]] = self::_setType($matches[5]);
                } else {
                    $info['rra'][(int) $matches[1]][$matches[2]][(int) $matches[3]][$matches[4]] = self::_setType($matches[5]);
                }
            } else {
                echo "NOT MATCHED: $line\n";
            }
        }
        return $info;
    }
    
    private static function _setType($value) {
        if ($value === 'NaN') {
            return null;
        } elseif (preg_match('/^\d+$/', $value)) { // integer
            return (int) $value;
        } elseif (preg_match('/^\d+\.\d+e\+\d{2}/', $value)) {
            return (double) $value;
            
        } else {
            return $value;
        }
        
    }
}
