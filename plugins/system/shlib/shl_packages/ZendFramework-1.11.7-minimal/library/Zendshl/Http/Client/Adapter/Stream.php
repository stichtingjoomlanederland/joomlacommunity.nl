<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zendshl_Http
 * @subpackage Client_Adapter
 * @version    $Id: Stream.php 23775 2011-03-01 17:25:24Z ralph $
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
defined('_JEXEC') or die;

/**
 * An interface description for Zendshl_Http_Client_Adapter_Stream classes.
 *
 * This interface decribes Zendshl_Http_Client_Adapter which supports streaming.
 *
 * @category   Zend
 * @package    Zendshl_Http
 * @subpackage Client_Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zendshl_Http_Client_Adapter_Stream
{
    /**
     * Set output stream
     *
     * This function sets output stream where the result will be stored.
     *
     * @param resource $stream Stream to write the output to
     *
     */
    public function setOutputStream($stream);
}
