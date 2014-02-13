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
 * @category   Mic
 * @package    Mic_Cache
 * @subpackage Mic_Cache_Time
 * @author     Jesse R. Mather <jrmather@gmail.com>
 * @copyright  2009 Nobody
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version    SVN: $Id: $
 * @filesource
 */

/**
 * Mic_Cache_Time_Record
 * 
 * @category   Mic
 * @package    Mic_Cache
 * @subpackage Mic_Cache_Time
 */
class Mic_Cache_Time_Record
{
    /**
     * Sets the property and unserializes the value if the name is 'data'
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        // unserialize the data field
        if ($name == 'data') {
            $value = unserialize($value);
        }
        
        $this->$name = $value;
    }
}
