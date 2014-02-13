<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Http
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see Mic_Object
 */
require_once 'Mic/Object.php';

/**
 * Mic_Http_Authentication_Digest
 *
 * @category  Mic
 * @package   Mic_Http_Authentication
 */
class Mic_Http_Authentication_Digest extends Mic_Object
{
    /**
     * 
     */
    private $_secret = 'S7cWSkd612JqUoSQoP78zHvidyqUsXX0xcCDpKRo2A4jaCxm8dObCuPnpTg4C3t';
    
    /**
     * 
     */
    private $_request;
    
    /**
     * 
     */
    public function __construct($secret=null, $request=null)
    {
        $this->_request = $request ? $request : new Mic_Request();
        $this->_secret = $secret ? $secret : $this->_secret;
    }
    
    /**
     * 
     */
    private function _expectedResponse($credentials, $password, $ha1Password=true)
    {
        $ha1 = $ha1Password ? $password : $this->_ha1($credentials, $password);
        $ha2 = md5(A(array($this->_request->method, $this->_request->path))->join(':'));
        
        return md5(A(array($ha1, $credentials['nonce'], $credentials['nc'], $credentials['cnonce'], $credentials['qop'], $ha2))->join(':'));
    }
    
    /**
     * 
     */
    private function _ha1($credentials, $password)
    {
        return md5(A(array($credentials['username'], $credentials['realm'], $password))->join(':'));
    }
    
    /**
     * 
     */
    private function _nonce($secret, $time=null)
    {
        $time = $time ? $time : time();
        $hashed = new Mic_Array($time, $secret);
        $digest = md5($hashed->join(':'));
        
        return base64_encode(S("{$time}:{$digest}")->replace("/\n/", '')->toS());
    }
    
    /**
     * 
     */
    private function _opaque($secret)
    {
        return md5($secret);
    }
    
    /**
     * username="jesse", realm="Test Authentication", nonce="", uri="/~jmather5/auth.php", algorithm=MD5, response="eab47240420bf2432aaa209f2c1daaa9", qop=auth, nc=00000001, cnonce="575c464e4b82bc57"
     */
    private function _parseDigest()
    {
        if (!isset($_SERVER['PHP_AUTH_DIGEST'])) {
            //throw new Mic_Http_Authentication_Exception("Digest authentication header is not present");
            return null;
        }
        
        $digest =  new Mic_String($_SERVER['PHP_AUTH_DIGEST']);
        $decoded = new Mic_Array();
        
        //Don't need to do this in PHP... ->replace('/^Digest\s+/', '')
        $digest->split(',')->map(function($pair) use (&$decoded) {
            list($key, $value) = S($pair)->rsplit('/\=/', 2);
            $decoded[S($key)->trim()->toS()] = S($value)->replace('/^"|"$/', '');
        });
        
        return $decoded->toA();
    }
    
    /**
     * 
     */
    private function _validateCredentials($realm, $callback)
    {
        $credentials = $this->_parseDigest();
        
        $valid = false;
        
        if ($this->_validateNonce($this->_secret, $credentials['nonce']) 
            && $realm == $credentials['realm']
            && $this->_opaque($this->_secret) == $credentials['opaque']
        ) {
            $password = $callback($credentials['username']);
            
            foreach (array(true, false) as $truth) {
                $response = $this->_expectedResponse($credentials, $password, $truth);
                
                $valid = $response == $credentials['response'] ? true : false;
            }
        }
        
        return $valid;
    }
    
    /**
     * 
     */
    private function _validateNonce($secret, $value, $timeout=300)
    {
        $time = S(base64_decode($value))->split(':')->first();
        $nonce = $this->_nonce($secret, $time);
        
        return $nonce == $value && abs($time - time()) <= $timeout;
    }
    
    /**
     * 
     */
    public function authenticate($realm='Realm', $callback)
    {
        return $this->_validateCredentials($realm, $callback);    
    }
    
    /**
     * 
     */
    public function header($realm) {
        //
        $nonce = $this->_nonce($this->_secret, time());
        $opaque = $this->_opaque($this->_secret, time());
        
        header("WWW-Authenticate: Digest realm=\"{$realm}\", qop=\"auth\", algorithm=MD5, nonce=\"{$nonce}\", opaque=\"{$opaque}\"");
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
            : "HTTP Digest: Access denied.\n";
    }
}