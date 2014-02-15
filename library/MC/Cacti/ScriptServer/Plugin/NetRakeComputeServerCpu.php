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
 * @package   MC_Cacti
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Cacti_ScriptServer_Plugin_NetRakeComputeServerCpu
 *
 * @category  MC
 * @package   MC_Cacti
 */
class MC_Cacti_ScriptServer_Plugin_NetRakeComputeServerCpu extends MC_Cacti_ScriptServer_Plugin
{
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
        
        $cmd = "{$ssh} -o ConnectTimeout={$timeout} "
                . ($key  ? "-i {$key} " : '')
                . ($user ? "{$user}@"  : '')
                . "{$hostname} "
                . "\"/bin/cat /var/log/system_check.log " 
                . "| /bin/grep Cpu | /usr/bin/tail -n 1 \" "
                . "| /bin/awk '{print $2,$4,$6,$8}' "
                . "| sed 's/\%//g'";
                
        $result = MC_Proc::exec($cmd);
        
        $result = array_combine(
            array('user', 'system', 'nice', 'idle'),
            split(' ', trim($result))
        );
        
        return $result;
    }
}