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
 
//require_once dirname(__FILE__) . '/Base.php';

/**
 * MC_Cacti_ScriptServer_Plugin_Cpu_Linux
 *
 * @category  MC
 * @package   MC_Cacti
 */
class MC_Cacti_ScriptServer_Plugin_Cpu_Netrake extends MC_Cacti_ScriptServer_Plugin_Cpu_Base
{   
    /**
     * 
     */
    public static function execute($params)
    {   
        $hostname   = isset($params['hostname']) 
                                              ? $params['hostname'] : false;
        $user       = isset($params['user'])  ? $params['user']     : 'cacti';
        $key        = isset($params['ssh_key'])   ? $params['ssh_key']      : false;
        $ssh        = (isset($params['ssh'])) ? $params['ssh']
                                              : '/usr/bin/env ssh';
        $timeout    = isset($params['ssh_timeout'])
                                              ? $params['ssh_timeout']  : 5;    
        
        $cmd = "{$ssh} -o ConnectTimeout={$timeout} "
                . ($key  ? "-i {$key} " : '')
                . ($user ? "{$user}@"  : '')
                . "{$hostname} "
                . '"/bin/cat /var/log/system_check.log '
                . '| /bin/grep Cpu | /usr/bin/tail -n 1 " '
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