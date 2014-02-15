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
 * @package   MC_Parameter
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @deprecated
 * @filesource
 */
 
/**
 * @see MC_Parameter
 */
require_once 'MC/Parameter.php';

trigger_error(__FILE__ . " is deprecated", E_USER_WARNING);

/**
 * MC_Parameter_Cli
 *
 * @category  MC
 * @package   MC_Parameter
 */
class MC_Parameter_Cli extends MC_Parameter
{
    public static function map()
    {
        
        $mapped = new MC_Array();
        $args   = new MC_Array($_SERVER['argv']);
        
        
        $loaner = false;
        $pass   = 0;
        
        $args->shift();
        
        foreach ($args as $arg) {
            $pass++;
            if($matches = S($arg)->rmatch('/^-(?<param>[\w]+)$/')) {
                //short option
                
                foreach (S($matches->param)->split() as $_opt)
                {
                    $mapped->$_opt = true;
                    $loaner        = $_opt;
                }
            } elseif($matches = S($arg)->rmatch('/^--(?<param>[\w]+)(?:(?<assign>=)(?<value>.*))?/')) {
                
                if ($matches->assign) {
                    $mapped->{$matches->param} = $matches->value;
                } else {
                    $mapped->{$matches->param} = true;
                    $loaner = $matches->param;
                }
            } elseif (S($arg)->match('--')) {
                $mapped->push($args->slice($pass)->join(' '));
                break;
            } else {
                // untagged option
                if($loaner) {  // look for a loaner that didn't have an
                               // assignment.  i.e. --param\s or -p\s
                    $mapped->{$loaner} = $arg;
                } else {
                    $mapped->push($arg);
                }
            }
            
        }
        
        return new self($mapped);
    }
}