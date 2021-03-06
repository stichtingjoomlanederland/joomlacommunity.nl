<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptRemovePostStatusACL extends EasyDiscussMaintenanceScript
{
	public static $title = "Remove Post Status ACL";
    public static $description = "Removing the ACL of Post Status as there are no longer being used";

    public function main()
    {
    	$db = ED::db();

        $query = 'SELECT COUNT(1) FROM `#__discuss_acl` WHERE `group` = ' . $db->quote('status');

        $db->setQuery($query);
        $result = (int) $db->loadResult();

        if (!$result) {
            return true;
        }

    	$query = 'DELETE FROM `#__discuss_acl` WHERE `group` = ' . $db->quote('status');

        $db->setQuery($query);
        $db->query();

    	return true;
    }
}