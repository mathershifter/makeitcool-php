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
 * @package   Mic_Collection
 * @author    Jesse R. Mather <jrmather@gmail.com>
 * @copyright 2009-2010 Nobody
 * @license   MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @version   $Id: $
 * @filesource
 */

/**
 * @see Mic_Array
 */
require_once 'Mic/Array.php';

/**
 * Mic_Collection
 *
 * @category  Mic
 * @package   Mic_Collection
 */
if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
    class_alias('Mic_Array', 'Mic_Collection');
} else {
    throw new Mic_Exception("Class only available in PHP >= 5.3.0");
}