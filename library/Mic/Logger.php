<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Logger
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @deprecated
 * @filesource
 */
 
trigger_error(__FILE__ . " is deprecated", E_USER_WARNING);

/**
 * MC_Logger
 *
 * @category  MC
 * @package   MC_Logger
 */
class MC_Logger extends MC_Object
{
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	const EMERG   = 0;  // Emergency: system is unusable
	const ALERT   = 1;  // Alert: action must be taken immediately
	const CRIT    = 2;  // Critical: critical conditions
	const ERR     = 3;  // Error: error conditions
	const WARN    = 4;  // Warning: warning conditions
	const NOTICE  = 5;  // Notice: normal but significant condition
	const INFO    = 6;  // Informational: informational messages
	const DEBUG   = 7;  // Debug: debug messages
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	public static $format   = "%timestamp% %priorityName% (%priority%): %message%\n";
	public static $priority = self::INFO;
	public static $stream   = 'php://output';
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected static $_instance;
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $_priorities;
	protected $_stream;
	
	//public function __call($method, $params) {}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $stream
	 */
	public function __construct($stream=null)
	{
		$this->_stream = $stream ? $stream : fopen(self::$stream, 'a');
		
		$r = new ReflectionClass($this);
        $this->_priorities = array_flip($r->getConstants());
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $message
	 * @param unknown_type $priority
	 */
	public function write($message, $priority)
	{
		$event = array(
			'timestamp'    => @date('Y-m-d H:i:s'),
			'priorityName' => $this->_priorities[$priority],
			'priority'     => $priority,
			'message'      => $message
		);
		fwrite($this->_stream, $this->format($event));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $message
	 * @param unknown_type $priority
	 */
	public static function log($message, $priority=null)
	{		
		if (!self::$_instance) {
			self::$_instance = new self();
		}
		
		$priority = $priority ? $priority : self::$priority;
		
		self::$_instance->write($message, $priority);
	}
	
	/**
     * Formats data into a single line to be written.
     *
     * @param  array    $event    event data
     * @return string             formatted line to write to the log
     */
    public function format($event)
    {
        $output = self::$format;
        foreach ($event as $name => $value) {

            if ((is_object($value) && !method_exists($value,'__toString'))
                || is_array($value)) {

                $value = gettype($value);
            }

            $output = str_replace("%$name%", $value, $output);
        }
        return $output;
    }
}