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
 * @package   MC_Image
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see MC_Image_Abstract
 */
require_once 'MC/Image/Abstract.php';

/**
 * MC_Image_Png
 * 
 * @category  MC
 * @package   MC_Image
 */
class MC_Image_Png extends MC_Image_Abstract
{
    protected function _open($filename, array $options=array())
    {
        return imagecreatefrompng($filename);
    }
    
    protected function _write($image, $filename=null)
    {
        imagepng($image, $filename);
    }
}
