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
 * Mic_Cacti_ScriptServer_Parser_Mpstat_Linux
 *
 * @category  Mic
 * @package   Mic_Cacti
 */
class Mic_Cacti_ScriptServer_Parser_Mpstat_Linux
{
    /**
     * 
     */
    public static function parse($data)
    {
        // detect linux mpstat, first line starts with "Linux"
        if (!preg_match('/^Linux/', $data)) {
            return false;    
        }
    
        foreach(explode("\n", $data) as $line) {
            if ($matches = S($line)->rmatch(
                '/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}(?:\s+[A-Z]+)?\s+all'
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
                    'idle'   => $matches['idle']);
            }
        }
        
        
        throw new Mic_Cacti_ScriptServer_Parser_Exception("Failed to parse command output");
    }
}