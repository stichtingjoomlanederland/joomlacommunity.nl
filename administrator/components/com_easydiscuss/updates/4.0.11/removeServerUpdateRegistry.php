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

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptRemoveServerUpdateRegistry extends EasyDiscussMaintenanceScript
{
    public static $title = "Remove component updater registry in Joomla!";
    public static $description = "Remove component updater registry in Joomla! as the EasyDiscuss's updater services already obsolete.";

    public function main()
    {
        $db = ED::db();

        // lets get the component id.
        $query = "select " . $db->nameQuote('extension_id') . " from " . $db->nameQuote('#__extensions');
        $query .= " where " . $db->nameQuote('type') . " = " . $db->Quote('component') . " and " . $db->nameQuote('element') . " = " . $db->Quote('com_easydiscuss');
        $db->setQuery($query);

        $ext_id = $db->loadResult();

        if ($ext_id) {

            $query = "delete a, b";
            $query .= "    from `#__update_sites_extensions` as a";
            $query .= "        inner join `#__update_sites` as b on a.`update_site_id` = b.`update_site_id`";
            $query .= " where a.`extension_id` = " . $db->Quote($ext_id);

            $db->setQuery($query);
            $db->query();
        }

        return true;
    }
}
