<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * MC PHP Framework
 *
 * PHP version 5.2+
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  MC
 * @package   MC_Proc
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * MC_Proc_Resource
 *
 * @category  MC
 * @package   MC_Proc
 */
class MC_Proc_Resource extends MC_Resource implements Iterator
{
    /**
     * Alias for write
     *
     * @see MC_Resource::write
     */
    public function send($data)
    {   
        return $this->write($data);
    }
}