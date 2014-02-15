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
class MC_Cacti_ScriptServer_Plugin_Cpu_Linux extends MC_Cacti_ScriptServer_Plugin_Cpu_Base
{   
    /**
     * 
     */
    public static function execute($params)
    {   
        $command     = '';
        
        $hostname    = isset($params['hostname']) ? $params['hostname'] : false;
        $user        = isset($params['user'])     ? $params['user']     : 'cacti';
        $mpstat      = isset($params['path'])     ? $params['path']     : 'mpstat';
        
        $ssh         = (isset($params['ssh']))    ? $params['ssh']      : 'ssh';
        $ssh_key     = isset($params['ssh_key'])  ? $params['ssh_key']  : false;
        $ssh_timeout = isset($params['ssh_timeout'])
                                                  ? $params['ssh_timeout'] : 5;
            
        if ($hostname && !S($hostname)->rmatch('/^(?:127.0.0.[0-9]{1,3}|localhost)/')) {
            $command = "{$ssh} -o ConnectTimeout={$ssh_timeout} "
                . ($ssh_key  ? "-i {$ssh_key} " : ' ')
                . ($user ? "{$user}@"   : '')
                . "{$hostname} {$mpstat} 1 1";
        } else {
            $command = "$mpstat 1 1";
        }
        
        return MC_Proc::exec($command);;
    }
    
    public static function parse($result)
    {
        foreach(explode("\n", $result) as $line) {
                        
            if ($matches = S($line)->rmatch(
                '/^Average:\s+all'
                . '\s+(?<user>[0-9\.]+)'   # - user level 
                . '\s+(?<nice>[0-9\.]+)'   # - user level with nice priority
                . '\s+(?<sys>[0-9\.]+)'    # - system level (kernel)
                . '\s+(?<iowait>[0-9\.]+)' # - percentage of time that th e CPU 
                                           #   or CPUs were idle during which 
                                           #   the system had an outstanding 
                                           #   disk I/O request
                . '\s+(?<irq>[0-9\.]+)'    # - percentage of time spent by the CPU
                                           #   or CPUs to service interrupts
                . '\s+(?<soft>[0-9\.]+)'   # - percentage of time spent by the CPU
                                           #   or CPUs to service softirqs.
                . '\s+(?<steal>[0-9\.]+)'  # - Show the percentage of time spent
                                           #   in  involuntary  wait  by  the
                                           #   virtual  CPU  or CPUs while the
                                           #   hypervisor was servicing another
                                           #   virtual processor.
                . '\s+(?<idle>[0-9\.]+)'   # - idle and the system did not have an 
                                           #   outstanding disk I/O request.
                . '\s+(?<intrs>[0-9\.]+)'  # - total number of interrupts received
                                           #   per second by the CPU or CPUs.
                . '/')
            ) {
                
                return array(
                    'user'   => $matches['user'],
                    'nice'   => $matches['nice'],
                    'system' => $matches['sys'],
                    'idle'   => $matches['idle']
                );
            } elseif ($matches = S($line)->rmatch(
                '/^Average:\s+all'
                . '\s+(?<user>[0-9\.]+)'   # - user level 
                . '\s+(?<nice>[0-9\.]+)'   # - user level with nice priority
                . '\s+(?<sys>[0-9\.]+)'    # - system level (kernel)
                . '\s+(?<idle>[0-9\.]+)'   # - idle and the system did not have an 
                                           #   outstanding disk I/O request.
                . '\s+(?<intrs>[0-9\.]+)'  # - total number of interrupts received
                                           #   per second by the CPU or CPUs.
                . '/')
            ) {
                return array(
                    'user'   => $matches['user'],
                    'nice'   => $matches['nice'],
                    'system' => $matches['sys'],
                    'idle'   => $matches['idle']
                );
            }
        }
        
        throw new MC_Cacti_ScriptServer_Parser_Exception("Failed to parse command output");   
    }
    
}