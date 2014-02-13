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
 * Mic_Cacti_ScriptServer_Plugin_Cpu_Solaris
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer_Plugin_Cpu_Solaris extends Mic_Cacti_ScriptServer_Plugin_Cpu_Base
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
        
        return Mic_Proc::exec($command);;
    }
    
    public static function parse($result)
    {
        // sorts results foreach processor, averaged later
        $user   = array();
        $system = array();
        $nice   = array();
        $idle   = array();
    
        foreach(explode("\n", $result) as $line) {
            if ($matches = S($line)->rmatch(
                '/\s+(?<cpu>[0-9]+)'         # processor (set) ID
                . '\s+(?:[0-9]+)'        # minor faults
                . '\s+(?:[0-9]+)'        # major faults
                . '\s+(?:[0-9]+)'        # inter-processor cross-calls
                . '\s+(?:[0-9]+)'        # interrupts
                . '\s+(?:[0-9]+)'        # interrupts as threads
                . '\s+(?:[0-9]+)'        # context switches
                . '\s+(?:[0-9]+)'        # involuntary context switches
                . '\s+(?:[0-9]+)'        # thread migrations (to another processor)
                . '\s+(?:[0-9]+)'        # spins on mutexes
                . '\s+(?:[0-9]+)'        # spins  on  readers/writer  locks
                . '\s+(?:[0-9]+)'        # system calls
                . '\s+(?<user>[0-9]+)'   # percent user time
                . '\s+(?<system>[0-9]+)' # percent system time
                . '\s+(?<nice>[0-9]+)'   # no longer calculated, fake nice
                . '\s+(?<idle>[0-9]+)'   # percent idle time
                . '/')
            ) {
                $cpu = $matches['cpu'];
                
                $user[$cpu]   = $matches['user'];
                $system[$cpu] = $matches['system'];
                $nice[$cpu]   = $matches['nice'];
                $idle[$cpu]   = $matches['idle'];
            }
        }
    
        return array(
            'user'   => (count($user)   ? array_sum($user) / count($user) : 'U'),
            'system' => (count($system) ? array_sum($system) / count($system)
                                                                          : 'U'),
            'nice'   => (count($nice)   ? array_sum($nice) / count($nice) : 'U'),
            'idle'   => (count($idle)   ? array_sum($idle) / count($idle) : 'U')
        );
    }
    
}