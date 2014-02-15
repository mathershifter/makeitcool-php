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
 * MC_Rrd_Parser_Xport
 *
 * @category   MC
 * @package    MC_Rrd
 */
class MC_Rrd_Parser_Xport extends MC_Rrd_Parser_Abstract
{
    /**
     * 
     * @param array $response
     * @return array
     */
    public static function parse($response)
    {
        $_response = array();
        $_metadata = array();
        
        $xml = new SimpleXMLElement(join('', $response));
        
        $legend = $xml->meta->legend->children();
        
        foreach ($xml->data->row as $row) {
            
            $dup_keys = array();

            $_row = array();
            $timestamp = (int) $row->t;
            $i=0;
            foreach ($row->v as $value) {

                $key = "{$legend->entry[$i]}";
                
                if (array_key_exists($key, $_row)) {
                  
                    $dup_keys[$key] = array_key_exists($key, $dup_keys) ?  ++$dup_keys[$key] : 1;
                    
                    $key .= '_' . $dup_keys[$key];
                }
                
                if ("$value" === "NaN") {
                    $_row[$key] = null;
                } else {
                    $_row[$key] = (double) $value;
                }
                
                $i++;
            }
            
            // don't append an array of all nulls to the results
            if (array_diff($_row, array(null))) {
                array_push($_response, array_merge(array('timestamp' => $timestamp), $_row));
            }
        }
        return array('metadata' => (array) $xml->meta, 'data' => $_response);
    }
}
