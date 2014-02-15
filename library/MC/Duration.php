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
 * @package   MC_Duration
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see MC_Object
 */
require_once 'MC/Object.php';

/**
 * MC_Duration
 *
 * @category  MC
 * @package   MC_Duration
 */
class MC_Duration extends MC_Object
{
	// unit durations
    const YEAR   = 31536000; // (86400 * 365)
    const MONTH  =  2592000; // (86400 * 7 * 4) + (86400 * 2) or 30 days
    const WEEK   =   604800; // (86400 * 7)
    const DAY    =    86400; // (60 * 60 * 24)
    const HOUR   =     3600; // (60 * 60)
    const MINUTE =       60;
	
    /**
     * Duration in seconds
     *
     * @var integer
     */
    private $_duration;
    
    /**
     * Duration broken down into individual units
     *
     * @var array
     */
    private $_parts = array(
        'years'   => 0,
        'months'  => 0,
        'weeks'   => 0,
        'days'    => 0,
        'hours'   => 0,
        'minutes' => 0,
        'seconds' => 0
    );
    
    /**
     * Breaks the duration down into units
     * 
     * @param integer $duration
     */
    public function __construct($duration)
    {
        $this->_duration = $duration;
        
        if ($duration >= self::YEAR) {
            $this->years = floor($duration / self::YEAR);
            // reset seconds to the remainder
            $duration = $duration % self::YEAR;
        }
        
        if ($duration >= self::MONTH) {
            $this->months = floor($duration / self::MONTH);
            // reset seconds to the remainder
            $duration = $duration % self::MONTH;
        }
        
        if ($duration >= self::WEEK) {
            $this->weeks = floor($duration / self::WEEK);
            // reset seconds to the remainder
            $duration = $duration % self::WEEK;
        }
        
        if ($duration >= self::DAY) {
            $this->days = floor($duration / self::DAY);
            
            $duration = $duration % self::DAY;
        }
        
        if ($duration >= self::HOUR) {
            $this->hours = floor($duration / self::HOUR);
            
            $duration = $duration % self::HOUR;
        }
        
        if ($duration >= self::MINUTE) {
            $this->minutes = floor($duration / self::MINUTE);
            
            $duration = $duration % self::MINUTE;
        }
        
        if ($duration > 0) {
            $this->seconds = $duration;
        }
    }
    
    /**
     * XXX
     *
     * @param string $name
     * @return integer
     */
    public function __get($name)
    {
        return $this->_parts[$name];
    }
    
    /**
     * XXX
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    private function __set($name, $value)
    {
        $this->_parts[$name] = $value;
    }
    
    /**
     * XXX 
     *
     * @return string
     */
    public function toIso()
    {
        return                                           'P'
            . ($this->years      > 0  ? $this->years   . 'Y' : '')
            . ($this->months     > 0  ? $this->months  . 'M' : '')
            . ($this->weeks      > 0  ? $this->weeks   . 'W' : '')
            . ($this->days       > 0  ? $this->days    . 'D' : '')
            . (($this->hours     > 0 
               || $this->minutes > 0
               || $this->seconds > 0) ?                  'T' : '')
            . ($this->hours      > 0  ? $this->hours   . 'H' : '')
            . ($this->minutes    > 0  ? $this->minutes . 'M' : '')
            . ($this->seconds    > 0  ? $this->seconds . 'S' : '')
        ;
    }
    
    /**
     * Alias for toIso
     */
    public function isoize() { return $this->toIso(); }
    
    /**
     * Alias for toIso
     */
    public function isonate()  { return $this->toIso(); }
    
    /**
     * XXX
     * 
     * @return string
     */
    public function humanize($separator=' ', $fuzziness=2)
    {
        $parts     = array();
        
        if ($fuzziness < 1) {
            require_once 'MC/Duration/Exception.php';
            throw new MC_Duration_Exception("Funzziness must not be less " .
                                             "than 1");
        }
        
        if ($this->years > 0) {
            $parts[] = $this->years . ' year' . ($this->years != 1 ? 's' : '');
        }
        
        if ($this->months > 0 && count($parts) < $fuzziness) {
            $parts[] = $this->months . ' month'  .
                       ($this->months > 1 ? 's' : '');
        }
        
        if ($this->days > 0 && count($parts) < $fuzziness) {
            $parts[] = $this->days . ' day' .
                       ($this->days > 1 ? 's' : '');
        }
        
        if ($this->hours > 0 && count($parts) < $fuzziness) {
            $parts[] = $this->hours . ' hour' .
                       ($this->hours > 1 ? 's' : '');
        }
        
        if ($this->minutes > 0 && count($parts) < $fuzziness) {
            $parts[] = $this->minutes . ' minute' .
                       ($this->minutes > 1 ? 's' : '');
        }
        
        if (($this->seconds > 0 || empty($parts)) && count($parts) < $fuzziness
        ) {
            $parts[] = $this->seconds . ' second' . 
                       ($this->seconds != 1 ? 's' : '');
        }
        
        return join($separator, $parts);
    }
    
    /**
     * XXX
     * 
     */
    public function toI()
    {
        return $this->_duration;
    }
    
    /**
     * XXX
     * 
     */
    public function toS()
    {
        return $this->humanize();
    }
    
    /**
     * XXX
     * 
     */
    public function __toString()
    {
        return $this->toS();
    }
    
    /**
     * XXX
     * 
     */
    public function jsonize()
    {
        return json_encode($this->_parts);
    }
    
    /**
     * XXX
     * 
     */
    public function serialize()
    {
        return serialize($this->_parts);
    }
}
