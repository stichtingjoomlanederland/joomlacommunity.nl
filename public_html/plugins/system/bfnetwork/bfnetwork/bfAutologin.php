<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://myJoomla.guru/
 * @see       https://myWP.guru/
 * @see       https://mySites.guru/
 * @see       https://www.phil-taylor.com/
 *
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 *
 * If you have any questions regarding this code, please contact phil@phil-taylor.com
 */

require 'bfEncrypt.php';

/*
 * If we have got here then we have already passed through decrypting
 * the encrypted header and so we are sure we are now secure and no one
 * else cannot run the code below.
 */

// Pretend we are /administrator/index.php
define('_JEXEC', 1);

// Joomla 2.5.0 an onwards - auto login for Joomla 1.5 sites not a feature of myJoomla.com
define('JPATH_BASE', realpath(__DIR__.'/../../../../administrator/'));

require_once JPATH_BASE.'/includes/defines.php';
require_once JPATH_BASE.'/includes/framework.php';

// Load the application, instantiate things
$app = JFactory::getApplication('administrator');

// Load Joomla 2.5 dependances
if (function_exists('jimport')) {
    jimport('joomla.user.authentication');
}

// Load more dependances to instantiate them
JAuthentication::getInstance();

// Load Joomla 2.5 dependances
if (class_exists('JPluginHelper')) {
    JPluginHelper::importPlugin('user');
}

// Populate the \Joomla\CMS\User\User user object with user data
$user = JFactory::getUser();
$user->load((int) $dataObj->id);

$subfolderIfAny = null;

if (is_array($_SERVER) && array_key_exists('REQUEST_URI', $_SERVER)) {
    $subfolderIfAny = str_replace('/plugins/system/bfnetwork/bfnetwork/bfAutologin.php', '', $_SERVER['REQUEST_URI']);
}

// Load the required user from the database - Bail out if that user doesnt exist
if (!$user->id) {
    header('Location: '.$subfolderIfAny.'/');
    die;
}

// Construct a faked response-object
$response = new JAuthenticationResponse();

$response->type          = 'Joomla'; // ?
$response->email         = $user->email;
$response->fullname      = $user->name;
$response->username      = $user->username;
$response->password      = 'Not Actually Needed';
$response->language      = $user->getParam('language'); // Not tested
$response->status        = JAuthentication::STATUS_SUCCESS; // Woot Woot!
$response->error_message = null; // to be sure

// Pass control to plugins to do the actual login
$app->triggerEvent('onUserLogin', array((array) $response, array('action' => 'core.login.admin')));

// redirect to allow user access
header('Location: '.$subfolderIfAny.'/administrator/index.php?'.$dataObj->adminUrlAppend);
