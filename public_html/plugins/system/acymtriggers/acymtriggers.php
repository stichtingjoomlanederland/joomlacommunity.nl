<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.1.4
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

class plgSystemAcymtriggers extends JPlugin
{
    var $oldUser = null;

    public function initAcy()
    {
        $helperFile = rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acym'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';
        if (!file_exists($helperFile) || !include_once $helperFile) return false;

        return true;
    }

    public function onUserBeforeSave($user, $isnew, $new)
    {
        if (is_object($user)) $user = get_object_vars($user);

        $this->oldUser = $user;

        return true;
    }

    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        if (is_object($user)) $user = get_object_vars($user);

        if ($success === false || empty($user['email']) || !$this->initAcy()) return true;

        $userClass = acym_get('class.user');
        if (!method_exists($userClass, 'synchSaveCmsUser')) return true;
        $userClass->synchSaveCmsUser($user, $isnew, $this->oldUser);

        return true;
    }

    public function onUserAfterDelete($user, $success, $msg)
    {
        if (is_object($user)) $user = get_object_vars($user);

        if ($success === false || empty($user['email']) || !$this->initAcy()) return true;

        $userClass = acym_get('class.user');
        if (!method_exists($userClass, 'synchDeleteCmsUser')) return true;
        $userClass->synchDeleteCmsUser($user['email']);

        return true;
    }
}
