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
 * @package   Mic_Template2
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   SVN: $Id: $
 * @filesource
 */

/**
 * @see Mic_String
 */
require_once 'Mic/String.php';

/**
 * Mic_Template
 *
 * @category  Mic
 * @package   Mic_Template
 */
class Mic_Template2 extends Mic_String
{
    private $_paths;
    
    private $_replacement;
    
    /**
     * 
     */
    private function _find($path, $data)
    {
        if (!$data instanceof Mic_Array) {
            $data = new Mic_Array($data);
        }
        
        # first check for the path as a key
        if ($data->hasKey($path)) {
            return $data[$path];
        }
        
        list($_root, $_path) = S($path)->split('.', 2);
        
        if (!$data->hasKey($_root)) {
            throw new Mic_Template_Exception("Index '$_root' not found in data");
        }
            
        if ($_path) {
            return $this->_find($_path, $data[$_root]);
        } else {
            return $data[$_root];
        }
        
    }
    
    private function _getPaths($template)
    {
        if ($tags = S($template)->rmatchAll('/(?:\{(?<tags>[\w\ \.]+)\})/')->tags) {
            return $tags->unique();
        }
    }
    
    protected function _init($args)
    {
        $template = array_shift($args);
        $this->_replacement = !empty($args) ? array_shift($args) : array();
        
        if (Mic_File::exists($template)) {
            if (!is_readable($template)) {
                //
                require_once 'Mic/Template/Exception.php';
                throw new Mic_Template_Exception("Failed to read file: $file");
            }
            
            $tempate = Mic_File::open($template)->read();
        }
        
        $this->_paths = $this->_getPaths($template);
        
        return $template;
    }
    
    
    public static function process($template, $replacement)
    {
        $template = new self($template, $replacement);
        
        return $template->replace();
    }
    
    public function replace()
    {
        $replaced = new Mic_String("{$this}");
        foreach ($this->_paths as $path) {
            
            try {
                $replacement = $this->_find($path, $this->_replacement);
                
                $regex = "/\{" . addcslashes($path, "|:.+*?[^]($)<>-") . "\}/";
                
                $replaced = $replaced->replace($regex, $replacement);
            } catch (Mic_Template_Exception $e) {
                // pass
                //echo $e->getMessage() . "\n";
            }
        }
        
        return $replaced;
    }
}
    