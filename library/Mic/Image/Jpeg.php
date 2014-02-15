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
 * @filesource
 */

/**
 * @see MC_Image_Abstract
 */
require_once 'MC/Image/Abstract.php';

/**
 * MC_Image_Jpeg
 * 
 * @category  MC
 * @package   MC_Image
 */
class MC_Image_Jpeg extends MC_Image_Abstract
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
