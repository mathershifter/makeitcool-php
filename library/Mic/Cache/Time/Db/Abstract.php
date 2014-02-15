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
 * @category   MC
 * @package    MC_Cache
 * @subpackage MC_Cache_Time
 * @author     Jesse R. Mather <jrmather@gmail.com>
 * @copyright  2009 Nobody
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version    SVN: $Id: $
 * @filesource
 */

/**
 * @see MC_Cache_Time_Record 
 */
require_once 'MC/Cache/Time/Record.php';

/**
 * MC_Cache_Time_Db_Abstract
 * 
 * @category   MC
 * @package    MC_Cache
 * @subpackage MC_Cache_Time
 */
abstract class MC_Cache_Time_Db_Abstract
{
    protected $_handle;
        
    protected $_schema = array(
        'tables' => array(
            #'metadata' => array(
            #    'columns' => array(
            #        'id'        => 'integer primary key',
            #       'key'       => 'text',
            #        'lifetime'  => 'integer'
            #        /*,'interval'  => 'integer',*/
            #    ),
            #    'references' => array(),
            #    'indexes' => array(
            #        array(
            #            'name'    => 'metadata_uid',
            #            'columns' => array('key'),
            #            'unique'  => true
            #        ),
            #        array('name' => 'key', 'columns' => array('key')),
            #    ),
            #    
            #),  
            #'data' => array(
            #    'columns' => array(
            #        'metadata_id' => 'integer',
            #        'timestamp' => 'integer',
            #        'data'      => 'blob',
            #    ),
            #    'references' => array(
            #        'metadata_id' => array('table' => 'metadata', 'column' => 'id', 'delete' => 'cascade')
            #    ),
            #    'indexes' => array(
            #        array(
            #            'name'    => 'data_uid',
            #            'columns' => array('metadata_id', 'timestamp DESC'),
            #            'unique'  => true
            #        ),
            #        array(
            #            'name'    => 'timestamp',
            #            'columns' => array('timestamp DESC')
            #        )
            #    )
            #)
            
            'cache' => array(
                'columns'    => array(
                    'key'       => 'text',
                    'timestamp' => 'integer',
                    'lifetime'  => 'integer',
                    'data'      => 'blob'
                ),
                'references' => array(),
                'indexes'    => array(
                    array(
                        'name'    => 'data_uid',
                        'columns' => array('key', 'timestamp DESC'),
                        'unique'  => true
                    ),
                    array(
                        'name'    => 'timestamp',
                        'columns' => array('timestamp DESC')
                    )
                )
            )
        )
    );
    
    public function __construct($options)
    {
        $this->_handle = $this->_connect($options);
        
        $this->_create($this->_schema);
        
        $this->_purge();
    }
    
    abstract protected function _connect($params);
    
    abstract protected function _create($schema);
    
    abstract protected function _purge();
    
    abstract public function select($key, $start, $end);
    
    abstract public function delete($key, $start, $end);
    
    abstract public function insert($key, $data, $lifetime, $timestamp);
}
