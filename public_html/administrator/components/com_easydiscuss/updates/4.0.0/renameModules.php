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

class EasyDiscussMaintenanceScriptRenameModules extends EasyDiscussMaintenanceScript
{
    public static $title = "Renaming Modules name";
    public static $description = "Renaming modules name to follow the new name standard.";

    public function main()
    {
        $db = ED::db();

        // mod_recentdiscussions -> mod_easydiscuss_recentdiscussions
        // mod_ask -> mod_easydiscuss_ask
        
        $oldNames = array('mod_recentdiscussions', 'mod_ask');
        $newNames = array('mod_easydiscuss_recentdiscussions', 'mod_easydiscuss_ask');

        for ($i=0; $i<count($oldNames); $i++) {

            // jos_modules
            $query = array();
            $query[] = 'UPDATE ' . $db->qn('#__modules') . ' SET ' . $db->qn('module') . '=' . $db->Quote($newNames[$i]);
            $query[] = 'WHERE ' . $db->qn('module') . '=' . $db->Quote($oldNames[$i]);

            $query = implode(' ', $query);

            $db->setQuery($query);
            $state = $db->Query();
        }
       
        return $state;
    }
}
