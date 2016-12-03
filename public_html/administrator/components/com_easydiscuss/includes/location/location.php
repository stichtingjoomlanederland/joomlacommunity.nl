<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussLocation extends EasyDiscuss
{
    public function __construct()
    {
        parent::__construct();

        // Initialize the location provider
        $this->provider = $this->getProvider();
    }

    public static function factory($id = null, $type = null)
    {
        return new self($id, $type);
    }

    public function getProvider($provider = 'maps')
    {
        $file = __DIR__ . '/providers/' . strtolower($provider) . '.php';

        require_once($file);

        $className = 'EasyDiscussLocation' . ucfirst($provider);
        $obj = new $className;

        return $obj;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array(array($this->provider, $method), $arguments);
    }
}
