<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Mic PHP Framework
 *
 * PHP version 5.x
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  Mic
 * @package   Mic_Image
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see Mic_Image_Abstract
 */
require_once 'Mic/Image/Abstract.php';

/**
 * Mic_Image_Png
 * 
 * @category  Mic
 * @package   Mic_Image
 */
class Mic_Image_Png extends Mic_Image_Abstract
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
