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
 * @package   MC_Http_Request
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Http_Request
 * 
 * Packages HTTP request data into an object
 *
 * @category  MC
 * @package   MC_Http_Request
 */
class MC_Http_Request extends MC_Object
{
    private static $_instance;
    
    /**
     * XXX
     * 
     * @var array
     */
    private $_properties = array(
        'method'     => null,
        'scheme'     => null,
        'path'       => null,
        'query'      => null,
        'params'     => null,
        'uri'        => null,
        //'url'        => null,
        'remote'     => null,
        'file'       => null,
        'host'       => null,
        'port'       => null,
        'time'       => null,
        'headers'    => null
    );
    
    /**
     * XXX
     * 
     * @return MC_Http_Request
     */
    public function __construct()
    {
        $this->method     = $_SERVER['REQUEST_METHOD'];
                
        $this->params     = new MC_Array(array_merge($_GET, $_POST));
        $this->path       = $_SERVER['SCRIPT_NAME'];
        $this->query      = $_SERVER['QUERY_STRING'];
        $this->file       = $_SERVER['SCRIPT_FILENAME'];
        
        $this->remote     = $_SERVER['REMOTE_ADDR'] . ':'
                          . $_SERVER['REMOTE_PORT'];
                          
        $this->host       = $_SERVER['SERVER_NAME'];
        $this->port       = $_SERVER['SERVER_PORT'];
        $this->scheme     = (isset($_SERVER['HTTPS']) 
                            && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        
        /**
         * MC_Time
         */                    
        $this->time       = new MC_Time($_SERVER['REQUEST_TIME']);
        
        /*
         * build the URL
         */
        $this->uri = $this->getUri(array(
            'scheme' => $this->scheme,
            'host'   => $this->host,
            'port'   => ($this->port != 443 && $this->port != 80)
                       ? $this->port : null,
            'path'   => $this->path,
            'query'  => $this->query
        ));
        
        $this->_setHeaders();
    }
    
    public function getInstance()
    {
        if (!self::$_instance instanceof self)
        {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * XXX
     * 
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    private function __set($name, $value)
    {
        $this->_properties[$name] = $value;
    }
    
    /**
     * XXX 
     *
     * @param mixed $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->_properties)) {
            throw new MC_Http_Exception("Unrecognized request property: $name");
        }
        
        return $this->_properties[$name];
    }
    
    /**
     * XXX 
     *
     * @param string|array $query
     * @return string
     */
    public function getPath($query=array())
    {
        return $this->path
            . (!empty($query) ? '?' . $this->getQuery($query) : null);
    }
    
    /**
     * XXX
     * 
     * @param string|array $query
     * @return string
     */
    public function p($query=array()) { return $this->getPath($query); }
    
    /**
     * Build a query string, merge given params
     *
     * @param mixed $params
     * @return string
     */
    public function getQuery($params=array())
    {
        // if params is not an array and is a string, parse it
        if (!is_array($params)) {
            if (!is_string($params)) {
                $params = array();
            } else {
                parse_str($params, $params);
            }
        }
        
        $query = array_merge($this->params->toArray(), $params);
        
        return http_build_query($query);
    }
    
    /**
     * Shorthand for query
     *
     * @param mixed $params
     * @return string
     */
    public function q($params=array()) { return $this->getQuery($params); }
    
    /**
     * Builds the request uri
     *
     * @param string|array $parts
     * @return string
     */
    public function getUri($parts=array())
    {
        // parts can be a string, if so parse it
        if (is_string($parts))
            $parts = parse_url($parts);
        
        // merge the original and new query, instead of overwriting
        $parts['query'] = $this->getQuery(
            (isset($parts['query']) ? $parts['query'] : array()));
        
        $uri = array_merge(parse_url($this->uri), $parts);
        
        return 
            ((isset($uri['scheme'])) ? $uri['scheme'] . '://' : '')
            . ((isset($uri['user'])) ? $uri['user'] . ((isset($uri['pass']))
                ? ':' . $uri['pass'] : '') .'@' : '')
            . ((isset($uri['host'])) ? $uri['host'] : '')
            . ((isset($uri['port'])) ? ':' . $uri['port'] : '')
            . ((isset($uri['path'])) ? $uri['path'] : '')
            . ((!empty($uri['query'])) ? '?' . $uri['query'] : '')
            . ((isset($uri['fragment'])) ? '#' . $uri['fragment'] : '')
        ;
    }
    
    /**
     * Alias for getUri
     *
     * @see uri
     * @param string|array $parts
     * @return string
     */
    public function getUrl($parts=array()) { return $this->getUri($parts); }
    
    /**
     * Alias for getUri
     *
     * @see uri
     * @param string|array $parts
     * @return string
     */
    public function u($parts=array()) { return $this->getUri($parts); }
    
    public function getHeader($name)
    {
        $name = S($name)->camelize();
        return $this->header["{$header}"];
    }
    
    public function h($name) { return $this->getHeader($name); }
    
    /**
     * 
     */
    private function _setHeaders()
    {
        $this->headers = new MC_Array();
        
        if ($headers = getallheaders()) {
            foreach ($headers as $header=>$value) {
                $header = S($header)->camelize();
                $this->headers["{$header}"] = $value;
            }
        }
    }
}