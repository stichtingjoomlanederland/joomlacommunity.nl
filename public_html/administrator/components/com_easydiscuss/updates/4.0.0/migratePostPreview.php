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

class EasyDiscussMaintenanceScriptMigratePostPreview extends EasyDiscussMaintenanceScript
{
    public static $title = "Migrate parent post's preview";
    public static $description = "This script is to migrate post raw content into preview column.";

    public function main()
    {
        $db = ED::db();
        $limit = 50;

        // step 1: retrieve the latest 50 questions.

        // $query = "select " . $db->nameQuote('content') . ", " . $db->nameQuote('content') . ", " . $db->nameQuote('content_type');
        $query = "select *";
        $query .= " FROM " . $db->nameQuote('#__discuss_posts');
        $query .= " WHERE " . $db->nameQuote('published') . " = " . $db->Quote('1');
        $query .= " AND " . $db->nameQuote('parent_id') . " = " . $db->Quote('0');
        $query .= " AND " . $db->nameQuote('preview') . " IS NULL";
        $query .= " ORDER BY " . $db->nameQuote('id') . " DESC";
        $query .= " LIMIT $limit";

        $db->setQuery($query);
        $items = $db->loadObjectList();

        $state = true;

        if ($items) {
            // step 2: now, based on the content_type, we need to convert the content.

            // preload posts
            ED::post($items);

            $ids = array();
            $cond = array();

            foreach ($items as $item) {

                $id = $item->id;

                $post = ED::post($id);

                $formattedContent = ED::formatContent($post);

                if ($formattedContent) {

                    $ids[] = $id;
                    $cond[] = "WHEN id = " . $db->Quote($id) . " THEN " . $db->Quote($formattedContent);
                }
            }

            if ($ids) {
                // now lets join the sql to form voltron force!
                $query = "update " . $db->nameQuote('#__discuss_posts');
                $query .= " set " . $db->nameQuote('preview') . " = (CASE ";
                $query .= implode(' ', $cond);
                $query .= " END)";
                $query .= " WHERE " . $db->nameQuote('id') . " IN (" . implode(',', $ids) . ")";

                // echo $query;exit;

                $db->setQuery($query);
                $state = $db->query();
            }

        }

        return $state;
    }

}
