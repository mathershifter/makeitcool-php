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
 * @package   Mic_Proc
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * Mic_File_Stat
 *
 * @category  Mic
 * @package   Mic_File
 */
class Mic_File_Stat extends Mic_Array
{
    public function _init($args)
    {
        
        $file = !empty($args) ? array_shift($args) : $file;
        
        if (is_resource($file)) {
            $stat = fstat($file);
        } elseif (Mic_File::exists($file)) {
            $stat = array_slice(stat($file), 13);
        } else {
            throw new Mic_File_Exception("File '$file' is not valid.");
        }
        
        return $stat;
    }
}