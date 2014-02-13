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
 * Mic_Image_Jpeg
 * 
 * @category  Mic
 * @package   Mic_Image
 */
class Mic_Image_Jpeg extends Mic_Image_Abstract
{
    /**
     * 
     * @return void
     */
    protected function _open($filename, array $options=array())
    {
        return imagecreatefromjpeg($filename);
    }
    
    /**
     * 
     * @return void
     */
    protected function _write($image, $filename=null)
    {
        imagejpeg($image, $filename);
    }
    
    /**
     *
     * @return array List of valid jpeg extensions
     */
    protected function _getExtensions()
    {
        return array('jpg', 'jpeg', 'jpe');
    }
    
    /**
     * Gets image type from classname
     *
     * @return string
     */
    protected function _getType()
    {
        return 'jpeg';
    }
}
