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
 * @filesource
 */

/**
 * @see MC_Cache_Time_Db_Abstract
 */
require_once 'MC/Cache/Time/Db/Abstract.php';

/**
 * MC_Cache_Time_Db_Sqlite
 * 
 * @category   MC
 * @package    MC_Cache
 * @subpackage MC_Cache_Time
 */
class MC_Cache_Time_Db_Sqlite extends MC_Cache_Time_Db_Abstract
{
    /**
     * 
     */
    protected function _connect($options)
    {
        $name = 'cache_time.sqlite';
        
        $path = isset($options['path']) ? $options['path'] : false;
        
        // sets to true if database does not exist yet
        $newDb = false;
        
        if (!$path) {
            // by default the database will be created in tmp
            $path = sys_get_temp_dir() . $name;
        } elseif (is_dir($path)) {
            // path is a directory, append the default file name
            $path = $path . '/' . $name;
        } elseif (preg_match('/\.(:?db|sqlite3?)$/', $path) && is_dir(dirname($path))) {
            // all included, nothing to do
        } else {
            require_once('MC/Cache/Time/Db/Exception.php');
            throw new MC_Cache_Time_Db_Exception("Failed to determine useable path from: "
                . $path);
        }
        
        //echo "Creating/Opening database: $path\n";
        
        try {
            if (!file_exists($path)) $newDb = true;
            $dbh = new PDO('sqlite:' . $path);
            
            // make sure any one can write to this database
            if ($newDb) chmod($path, 0666);
        } catch (PDOException $e) {
            require_once('MC/Cache/Time/Db/Exception.php');
            throw new MC_Cache_Time_Db_Exception("Failed to open database: $db. "
                . $e->getMessage());
        }
        
        return $dbh;
    }
    
    /**
     * 
     */
    public function select($key, $start, $end)
    {
        //echo "Getting...\n\tKEY: $key\n\tSTART: $start\n\tEND:   $end\n";
        #return $this->_query(
        #    'SELECT timestamp, data FROM metadata, data WHERE ' .
        #    'metadata.id = data.metadata_id AND key = ? ' .
        #    'AND timestamp BETWEEN ? AND ? ORDER BY timestamp ASC',
        #    array($key, $start, $end)
        #);
        
        return $this->_query(
            'SELECT timestamp, data FROM cache WHERE ' .
            'key = ? AND timestamp BETWEEN ? AND ? ORDER BY timestamp ASC',
            array($key, $start, $end)
        );
    }
    
    /**
     * 
     */
    public function insert($key, $data, $lifetime, $timestamp)
    {   
        //echo "Setting...\n\tKEY: $key\n\tLIFETIME: $lifetime\n\tTIMESTAMP: $timestamp\n";
        
        #$this->_execute('INSERT OR IGNORE INTO metadata ' . 
        #    '(key, lifetime) VALUES ' .
        #    '(?, ?)', array($key, $lifetime)
        #);
        #$metaData = array_shift($this->_query('SELECT id, lifetime ' .
        #        'FROM metadata WHERE key = ?', array($key)));
        
        #if ((int) $metaData->lifetime !== (int) $lifetime) {
        #    require_once('MC/Cache/Time/Db/Exception.php');
        #    throw new MC_Cache_Time_Db_Exception("Tried to insert a " .
        #        "duplicate metadata entry using a different lifetime " .
        #        "than the existing one: " .
        #        "[{$metaData->lifetime} !== {$lifetime}]");
        #}
                
        #if ($metaData->id > 0) {
        #    $this->_execute('INSERT OR IGNORE INTO data ' .
        #        '(metadata_id, timestamp, data) VALUES (?, ?, ?)',
        #        array($metaData->id, $timestamp, $data));
        #}
        $this->_execute('INSERT OR IGNORE INTO cache ' .
                '(key, timestamp, lifetime, data) VALUES (?, ?, ?, ?)',
                array($key, $timestamp, $lifetime, $data));
    }
    
    /**
     * 
     */
    public function delete($key, $start, $end)
    {
//        return $this->_execute(
//            'DELETE FROM ' . $this->_schema['table']
//            . ' WHERE key = ? AND timestamp BETWEEN ? AND ?',
//            array($key, $start, $end)
//        );
    }
    
    /**
     * 
     * @param $sql
     * @param $params
     * @return unknown_type
     */
    private function _query($sql, $params=null)
    {
        $sth = $this->_execute($sql, $params);
        return $sth->fetchAll(PDO::FETCH_CLASS, 'MC_Cache_Time_Record');
    }
    
    private function _execute($sql, $params=null)
    {
        //echo "PREPARING: $sql\n";
        $sth = $this->_prepare($sql);
           
        if ($sth->execute($params) === false) {
            $error = $this->_handle->errorInfo();
            require_once('MC/Cache/Time/Db/Exception.php');
            throw new MC_Cache_Time_Db_Exception("Failed to execute statement: "
                . $error[2] . '[' . $error[1] . ']');   
        }
        
        return $sth;
    }
    
    private function _prepare($statement)
    {
        $sth = $this->_handle->prepare($statement);
        if (!$sth) {
            $error = $this->_handle->errorInfo();
            require_once('MC/Cache/Time/Db/Exception.php');
            throw new MC_Cache_Time_Db_Exception('Failed to prepare statement: '
                . $error[2] . '[' . $error[1] . ']');
        }
        return $sth;
    }
    
    protected function _create($schema)
    {
        foreach ($schema['tables'] as $tableName=>$table) {
            // BEGIN - create table
            $columns = array();
            $createSql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . ' (';
            foreach ($table['columns'] as $name=>$type) {
                $columns[] = $name . ' ' . strtoupper($type);
            }
            
            $createSql .= join(', ', $columns);
            
            // create references
            foreach ($table['references'] as $column=>$foreign) {
                $createSql .= ', FOREIGN KEY (' . $column . ') REFERENCES ' .
                    $foreign['table'] . ' (' . $foreign['column'] . ') ' .
                    // not enforced prior to 3.6.19
                    'ON DELETE ' . strtoupper($foreign['delete']);
            }
            
            $createSql .= ');';
            $this->_execute($createSql);
            // END - create table
    
            // BEGIN - create indexes
            foreach ($table['indexes'] as $index) {
                $this->_execute('CREATE ' .
                    (isset($index['unique']) ? 'UNIQUE ' : '') . 'INDEX IF '. 
                    'NOT EXISTS ' . $index['name'] . ' ON ' . $tableName . ' ' .
                    '(' . join(', ', $index['columns']) . ')');
            }
            // END - create indexes
        }
    }
    
    protected function _purge()
    {
        return $this->_handle->exec('DELETE FROM cache WHERE '
            . 'timestamp < ' . time() . ' - lifetime');
    }
}
