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
 * @package   MC_Time
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see MC_Object
 */
require_once 'MC/Object.php';

/**
 * Base class for time objects
 *
 * @category  MC
 * @package   MC_Time
 */
class MC_Time
{
    // Constants - common formats
    const MYSQL   = 'Y-m-d H:i:s';
    const ISO8601 = 'c';
    const W3C     = 'c';
    const RFC2822 = 'r';
    const EPOCH   = 'U';
    
    /**
     * @var integer time as Unix epoch
     */
    private $_time;
    
    /**
     * 
     * @param integer $time
     * @return MC_Time
     */
    public function __construct($time=null)
    {
        $this->_time = isset($time) ? $time : time();
        
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $name
     */
    public function __call($name, $undef)
    {
    	return $this->{$name};
    }
    
    /**
     * 
     */
    public function __get($name)
    {
        $value = false;
        switch(S($name)->camelize()) {
            case 'second':
            case 'seconds':
            case 'secondOfMinute':
                $value = $this->format('s');
                break;
            case 'minute':
            case 'minutes':
            case 'minuteOfHour':
                $value = $this->format('i');
                break;
            case 'hour':
            case 'hours':
            case 'hourOfDay':
                $value = $this->format('H');
                break;
            case 'day':
            case 'days':
            case 'dayOfMonth':
                $value = $this->format('d');
                break;
            case 'dayOfWeek':
                $value = $this->format('w');
                break;
            case 'dayOfYear':
                $value = $this->format('z');
                break;
            case 'dayName':
            case 'dayLongName':
            case 'dayFullName':
                $value = $this->format('l');
                break;
            case 'dayShortName':
                $value = $this->format('D');
                break;
            case 'suffix':
            case 'daySuffix':
                $value = $this->format('S');
                break;
            case 'week':
            case 'weekOfYear':
                $value = $this->format('W');
                break;
            case 'month':
            case 'monthOfYear':
                $value = $this->format('m');
                break;
            case 'monthName':
            case 'monthFullName':
            case 'monthLongName':
                $value = $this->format('F');
                break;
            case 'monthShortName':
                $value = $this->format('M');
                break;
            case 'monthLength':
            case 'monthDays':
                $value = $this->format('t');
                break;
            case 'year':
                $value = $this->format('Y');
                break;
            case 'unix':
            case 'unixEpoch':
            case 'epoch':
                $value = $this->format('U');
                break;
            case 'w3c':
            case 'iso':
            case 'iso8601':
            	$value = $this->format('c');
                break;
            case 'rfc':
            case 'rfc2822':
            case 'imf':
            	$value = $this->format('r');
                break;
            default:
                $value = $this->format($name);
        }    
        
        return $value;
    }
    
    /**
     * Exports the time as RFC2822 formatted string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }
    
    /**
     * 
     */
    public function floor($interval = 'h')
    {
        switch ($interval) {
            case 'h':
                return self::create($this->year, $this->month, $this->day, $this->hour);
            case 'd':
                return self::create($this->year, $this->month, $this->day);
            case 'm':
                return self::create($this->year, $this->month);
            case 'y':
                return self::create($this->year);
            default:
                throw new MC_Time_Exception("Invalid interval: $interval");
        }
    }

    /**
     * 
     * @param string $format
     * @return string
     */
    public function format($format='r')
    {
        if (defined(__CLASS__ . '::' . $format)) {
            $format = constant(__CLASS__ . '::' . $format);
        }
        return date($format, $this->_time);
    }
    
    /**
     * 
     * @param integer $time
     * @return MC_Duration
     */
    public function getDuration($time=null)
    {
        if (get_class($time) === 'MC_Time') {
            $time = $time->toI();
        }

        $seconds = abs(($time ? $time : time()) - $this->_time);
        
        /**
         * @see MC_Duration
         */
        require_once 'MC/Duration.php';
        return new MC_Duration($seconds);
    }
    
    /**
     * Creates a new time object offset from the current time object
     * 
     * @param string $string
     * @return MC_Time
     */
    public function getOffset($string)
    {
        return new self(strtotime($string, $this->_time));
    }
    
    public static function create($year=1970, $month=1, $day=1, $hour=0, $minute=0, $second=0)
    {
        return new self(mktime($hour, $minute, $second, $month, $day, $year));
    }
    
    /**
     * Creates a new time object
     * 
     * @param integer $now
     * @return MC_Time
     */
    public static function at($time)
    {
        if (is_int($time))
            return new self($time);
        elseif (is_string($time))
            return self::parse($time);
    }
    
    /**
     * Creates a new time object
     * 
     * @param integer $now
     * @return MC_Time
     */
    public static function now()
    {
        return self::at(time());
    }
    
    
    
    /**
     * Creates a new time object
     * 
     * @param integer $now
     * @return MC_Time
     */
    public function offset($offset=0)
    {
        return new self($this->toI() + $offset);
    }
    
    /**
     * Creates a new time object from a string
     * 
     * @param unknown_type $string
     * @return MC_Time
     */
    public static function parse($string)
    {
        return new self(strtotime($string));
    }
    
    /**
     * Exports the time in unix epoch seconds
     * 
     * @return int
     */
    public function toI()
    {
        return (int) $this->_time;
    }
    
    public function toS()
    {
        return $this->__toString();
    }
}
