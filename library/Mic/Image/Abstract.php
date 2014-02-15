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
 * @see MC_Object
 */
require_once 'MC/Object.php';

/**
 * @see MC_Image 
 */
require_once 'MC/Image.php';
 
/**
 * MC_Image_Abstract
 * 
 * @category  MC
 * @package   MC_Image
 */
abstract class MC_Image_Abstract extends MC_Object
{
    /**
     * @var resource
     */
    private $_image;
    
    /**
     * @var array
     */
    private $_meta = array(
        'path'       => null,   # path to the image, not set unless image was
                                # loaded from a file
        'width'      => null,   # width of image in pixels
        'height'     => null,   # height of image in pixels
        'ratio'      => null,   # ratio of width to height
        'mimeType'   => null,   # registered mime type
        'type'       => null,   # image type
        'extensions' => array() # valid extensions for image
    );
    
    /**
     * 
     * @param mixed $image
     * @throws MC_Image_Exception
     */
    public function __construct($image)
    {
        if (!MC_Image::gdLoad()) {
            require_once 'MC/Image/Exception.php';
            throw new MC_Image_Exception("GD library is not loaded");
        }
        
        if (is_array($image)) {
            $this->_image = imagecreatetruecolor($image['width'], $image['height']);
        } else {
            // if image is already a GD resource, don't need to open it
            if (MC_Image::isImage($image)) {
                $this->_image = $image;
                $this->_setMetaData();
            } elseif (is_string($image)) {
                $this->open($image);
            } else {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception("Image is not a string or a resource");
            }
        }
    }
    
    /**
     * 
     */
    public function __destruct()
    {   
        if (MC_Image::isImage($this->_image)) {
            imagedestroy($this->_image);
        }
    }
    
    /**
     * Getter
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->_meta)) {
            require_once 'MC/Image/Exception.php';
            throw new MC_Image_Exception('Unknown property: ' . $name);
        }
        return $this->_meta[$name];
    }
    
    /**
     * Setter
     * 
     * @param $name
     * @param $value
     * @return void
     */
    protected function __set($name, $value)
    {
        $this->_meta[$name] = $value;
    }

    /**
     * 
     */
    public function open($file)
    {
        // XXX add error handling...
        $this->_image = $this->_open($file);
        $this->path = $file;
        $this->_setMetaData();
        
        return $this;
    }
    
    /**
     * 
     */
    public function write($filename=null, $overwrite=false)
    {
        if (!$filename || $filename === $this->path) {
            if ($overwrite=false) {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception('Will not overwrite original image unless explicitly told to do so.');
            }
            
            if (!$this->path) {
                require_once 'MC/Image/Exception.php';
                throw new MC_Image_Exception('Image was not loaded from a file.  A filename must be specified in this case.');
            }
            
            $filename = $this->_path;
        }
        
        $type = array_pop(explode('.', $filename));
        
        /*
         * if the image specified image is not the same type as the original,
         * convert it then write
         */
        if (!in_array($type, $this->extensions)) {
            return $this->convert($type)->write($filename);            
        } else {
            $this->_write($this->_image, $filename);    
            return $this;
        }
    }

    /**
     * 
     */
    public function display()
    {
        $this->_write($this->_image);
    }

    /**
     * 
     * @param $type
     * @return MC_Image_Abstract
     */
    public function convert($type)
    {
        return MC_Image::factory($this->_image, $type);
    }
    
    /**
     * Resizes the image
     *
     * @param integer $width
     * @param integer $height
     * @param bollean $lockRatio
     */
    public function resize($width, $height, $lockRatio=true)
    {
        // retrict resized image to original ratio
        if ($lockRatio) {
            //  adjust width or height to avoid streching
            if ($width / $height > $this->ratio) {
               $width = $height * $this->ratio;
            } else {
               $height = $width / $this->ratio;
            }
        }
        
        // create an empty image
        $tmp = imagecreatetruecolor($width, $height);
        
        // resize image mantain quality
        imagecopyresampled(
            $tmp,
            $this->_image,
            0, 0, 0, 0,
            $width,
            $height,
            $this->width,
            $this->height
        );
        
        $this->_image = $tmp;
        
        // re-set metadata
        $this->_setMetaData();
        
        return $this;
    }
    
    /**
     * Rotates the image
     * 
     * @param int $degrees
     * @param mixed $backgroundColor
     * @param boolean $ignoreTransparent
     */
    public function rotate($degrees, $backgroundColor=0, $ignoreTransparent=false)
    {
        $this->_image = imagerotate($this->_image, $degrees, $backgroundColor,
                                    $ignoreTransparent ? 1 : 0);
                                    
        return $this;
    }
    
    
    /**
     * 
     *
     * @return void
     */
    protected function _setMetaData()
    {
        $this->type = $this->_getType();
        $this->mimeType = $this->_getMimeType();
        $this->extensions = $this->_getExtensions();
        $this->width = imagesx($this->_image);
        $this->height = imagesy($this->_image);
        $this->ratio = $this->width / $this->height;
    }
    
    
    /**
     * Gets image type from classname, override if different than class
     * suffix
     *
     * @return string
     */
    protected function _getType()
    {
        return strtolower(array_pop(explode('_', get_class($this))));
    }
    
    /**
     * Returns list of possible extensions
     *
     * @return array
     */
    protected function _getExtensions()
    {
        return array($this->_getType());   
    }
    
    /**
     * Gets the mime type base on class name
     *
     * @return string
     */
    protected function _getMimeType()
    {
        return 'image/' . $this->_getType();
    }
    
    abstract protected function _open($file, array $options=array());
    abstract protected function _write($image, $file=null);
}
