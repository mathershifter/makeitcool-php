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
 * @package   MC_Http
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
 * MC_Http_Authentication_Basic
 *
 * @category  MC
 * @package   MC_Http_Authentication
 */
class MC_Http_Authentication_Basic extends MC_Object
{
    /**
     * 
     */
    public function authenticate($callback)
    {
        if ($credentials = $this->_getCredentials()) {
            return $callback($credentials['username'], $credentials['password']);    
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function header($realm)
    {
        header("WWW-Authenticate: Basic realm=\"{$realm}\"");
    }
    
    /**
     * 
     */
    public function request($realm, $message=null)
    {
        $this->header($realm);
        
        header('HTTP/1.1 401 Unauthorized');
        
        return $message 
            ? (is_callable($message) ? $message() : $message) 
            : "HTTP Basic: Access denied.\n";
    }
    
    private function _getCredentials()
    {
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = isset($_SERVER['PHP_AUTH_PW'])
                ? $_SERVER['PHP_AUTH_PW'] : '';  
            
            return array("username" => $username, "password" => $password);   
        }
        
        return false;
    }
}