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
 * @package   Mic_Cacti
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_Cacti_ScriptServer_Plugin_Mpstat
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer_Plugin_Mpstat extends Mic_Cacti_ScriptServer_Plugin
{
    /**
     *
     */
    protected function _init($params=array())
    {
        $this->parser = 'mpstat';
    }
    
    /**
     * 
     */
    protected function _execute($params=array())
    {
        $hostname   = isset($params['hostname']) 
                                              ? $params['hostname'] : false;
        $user       = isset($params['user'])  ? $params['user']     : 'cacti';
        $path       = isset($params['path'])  ? $params['path']     : 'mpstat';
        $key        = isset($params['key'])   ? $params['key']      : false;
        $ssh        = (isset($params['ssh'])) ? $params['ssh']
                                              : '/usr/bin/env ssh';
        $timeout    = isset($params['timeout'])
                                              ? $params['timeout']  : 5;    
        $interval   = isset($params['interval'])
                                              ? $params['interval'] : 1;
        $count      = isset($params['count']) ? $params['count']    : 1;
            
        if ($hostname) {
            $cmd = "{$ssh} -o ConnectTimeout={$timeout} "
                . ($key  ? "-i {$key} " : ' ')
                . ($user ? "{$user}@"   : '')
                . "{$hostname} {$path} {$interval} {$count}";
        } else {
            $cmd = $path;
        }
        
        return Mic_Proc::exec($cmd);
    }
}