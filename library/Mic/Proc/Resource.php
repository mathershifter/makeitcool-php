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
 * Mic_Proc_Resource
 *
 * @category  Mic
 * @package   Mic_Proc
 */
class Mic_Proc_Resource extends Mic_Resource implements Iterator
{
    /**
     * Alias for write
     *
     * @see Mic_Resource::write
     */
    public function send($data)
    {   
        return $this->write($data);
    }
}