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
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Image
 *
 * @category  MC
 * @package   MC_Image
 */
class MC_Image
{
    /**
     * Access class for thumbnail generators
     * 
     * @param string $filename
     * @param array $options
     * @param string $type
     * @return MC_Thumb_Abstract
     */
    public static function factory($image, $type=false)
    {
        // allow the type to be statically defiend
        if (!$type) {
            if (self::isImage($image)) {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception("Can not guess type from GD resource");
            } elseif (!is_string("{$image}")) {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception("Image must be a string to guess type");
            } elseif (!strstr("{$image}", '.')) {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception("Can't guess type without an extension");
            }
            
            $type = array_pop(explode('.', $image));
        }
        
        // prepend class path to type, if needed
        if (!strstr($type, __CLASS__)) {
            $type = __CLASS__ . '_' . ucfirst($type);          
        }
        
        require_once 'MC/Loader.php';
        MC_Loader::loadClass($type);
        
        return new $type($image);
    }  
    
    /**
     * Checks weather images is a GD resource
     * 
     * @param mixed $image
     * @return boolean
     */
    public static function isImage($image)
    {
        if (is_resource($image) && get_resource_type($image) === 'gd')
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Tries to load the GD extension if not already loaded
     * 
     * @return boolean
     */
    public static function gdLoad()
    {
        if (!extension_loaded('gd')) {
            $gd = null;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $gd = 'php_gd2.dll';
            } else {
                $gd = 'gd.so';
            }
            
            if (!@dl($gd)) {
                return false;
            }
        }
        
        return true;
    } 
}
