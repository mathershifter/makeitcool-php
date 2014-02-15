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
 * @package   MC_Proc
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_File_Stat
 *
 * @category  MC
 * @package   MC_File
 */
class MC_File_Stat extends MC_Array
{
    public function _init($args)
    {
        
        $file = !empty($args) ? array_shift($args) : $file;
        
        if (is_resource($file)) {
            $stat = fstat($file);
        } elseif (MC_File::exists($file)) {
            $stat = array_slice(stat($file), 13);
        } else {
            throw new MC_File_Exception("File '$file' is not valid.");
        }
        
        return $stat;
    }
}