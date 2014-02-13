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
 * @package   Mic_Math
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_Math
 *
 * @category  Mic
 * @package   Mic_Math
 */
class Mic_Math
{
    public static function avg($set)
    {
        return self::average($set);        
    }
    /**
     * Gets the average of all numeric values in the set
     */
    public static function average($set)
    {
        self::_convert($set);
        
        # remove non-numerics
        self::_sanitize($set);
        
        if ($set->count() === 0)
        {
            return null;        
        }
        
        return self::sum($set) / $set->count();
    }
    
    public static function last($set)
    {
        # remove non-numerics
        self::_sanitize($set);
        
        if ($set->count() === 0) {
            return null;
        }
        
        return $set->last();
    }
    
    /**
     * Gets the sum of all numeric values in the set 
     */
    public static function sum($set)
    {
        self::_convert($set);
        
        # remove non-numerics
        self::_sanitize($set);
        
        if ($set->count() === 0) {
            return null;
        }        
        
        return array_sum($set->toArray());
    }
    
    /**
     * Gets the max of all numeric values in the set
     */
    public static function max($set)
    {
        # remove non-numerics
        self::_sanitize($set);
        
        if ($set->count() === 0) {
            return null;
        }   
        
        return max($set->toArray());
    }
    
    /**
     * Gets the minimum of all numeric values in the set
     */
    public static function min($set)
    {
        # remove non-numerics
        self::_sanitize($set);
        
        if ($set->count() === 0) {
            return null;
        }   
        
        return min($set->toArray());
    }
    
    /**
     * 
     */
    public static function round($number, $places=0)
    {
        return is_numeric($number) ? round($number, $places) : null;
    }
    
    /**
     * Removes non-numeric values from set
     */
    private function _sanitize(&$set) {
        $_set = A();
        foreach ($set as $value) {
            if (is_numeric($value)) $_set->push($value);
        }
        
        $set = $_set;
    }
    
    /**
     * Converts an array to a Mic_Array
     *
     * @param array $set
     * @return void 
     */    
    private function _convert(&$set)
    {
        if (!$set instanceof Mic_Array) {
            $set = new Mic_Array($set);
        }
    }		
}
