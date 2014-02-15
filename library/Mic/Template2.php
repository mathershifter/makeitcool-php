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
 * @package   MC_Template2
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @filesource
 */

/**
 * @see MC_String
 */
require_once 'MC/String.php';

/**
 * MC_Template
 *
 * @category  MC
 * @package   MC_Template
 */
class MC_Template2 extends MC_String
{
    private $_paths;
    
    private $_replacement;
    
    /**
     * 
     */
    private function _find($path, $data)
    {
        if (!$data instanceof MC_Array) {
            $data = new MC_Array($data);
        }
        
        # first check for the path as a key
        if ($data->hasKey($path)) {
            return $data[$path];
        }
        
        list($_root, $_path) = S($path)->split('.', 2);
        
        if (!$data->hasKey($_root)) {
            throw new MC_Template_Exception("Index '$_root' not found in data");
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
        
        if (MC_File::exists($template)) {
            if (!is_readable($template)) {
                //
                require_once 'MC/Template/Exception.php';
                throw new MC_Template_Exception("Failed to read file: $file");
            }
            
            $tempate = MC_File::open($template)->read();
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
        $replaced = new MC_String("{$this}");
        foreach ($this->_paths as $path) {
            
            try {
                $replacement = $this->_find($path, $this->_replacement);
                
                $regex = "/\{" . addcslashes($path, "|:.+*?[^]($)<>-") . "\}/";
                
                $replaced = $replaced->replace($regex, $replacement);
            } catch (MC_Template_Exception $e) {
                // pass
                //echo $e->getMessage() . "\n";
            }
        }
        
        return $replaced;
    }
}
    