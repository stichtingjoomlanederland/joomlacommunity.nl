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

class EasyDiscussMaintenanceScriptMigratePostThread extends EasyDiscussMaintenanceScript
{
    public static $title = "Migrate parent post as thread";
    public static $description = "This script is to migrate parent posts into thread table.";

    public function main()
    {
        $db = ED::db();

        // step 1: migrate parent posts into thread

        $query = "insert into `#__discuss_thread` (`title`, `alias`, `created`, `modified`, `replied`, `user_id`, `post_id`, `user_type`, `poster_name`, `poster_email`, `content`, `preview`, `published`, `category_id`,";
        $query .= " `num_likes`, `num_negvote`, `ordering`, `vote`, `sum_totalvote`, `hits`, `islock`, `lockdate`, `featured`, `isresolve`, `isreport`, `answered`, `params`, `password`, `legacy`, `address`, `latitude`, `longitude`,";
        $query .= " `content_type`, `post_status`, `post_type`, `private`)";
        $query .= " select `title`, `alias`, `created`, `modified`, `replied`, `user_id`, `id`, `user_type`, `poster_name`, `poster_email`, `content`, `preview`, `published`, `category_id`, `num_likes`, `num_negvote`,";
        $query .= " `ordering`, `vote`, `sum_totalvote`, `hits`, `islock`, `lockdate`, `featured`, `isresolve`, `isreport`, `answered`, `params`, `password`, `legacy`, `address`, `latitude`, `longitude`, `content_type`,";
        $query .= " `post_status`, `post_type`, `private` from `#__discuss_posts` where `parent_id` = 0 and `thread_id` = 0";

        $db->setQuery($query);
        $state = $db->query();

        if ($state) {
            // step 2: now we need to sync the thread id into posts table
            $query = "update `#__discuss_posts` as a";
            $query .= " inner join `#__discuss_thread` as b on a.`id` = b.`post_id` or a.`parent_id` = b.`post_id`";
            $query .= " set a.`thread_id` = b.`id`";

            $db->setQuery($query);
            $state = $db->query();

            if ($state) {
                // step 3: re-sync data into thread table.
                $query = "update `#__discuss_thread` as a set";
                $query .= " a.`num_replies` = (select count(1) from `#__discuss_posts` as b1 where b1.`parent_id` = a.`post_id` and b1.`published` = 1),";
                $query .= " a.`num_fav` = (select count(1) from `#__discuss_favourites` as b5 where b5.`post_id` = a.`post_id`),";
                $query .= " a.`num_attachments` = (select count(1) from `#__discuss_attachments` as b6 where b6.`uid` = a.`post_id` and b6.`type` = " . $db->Quote(DISCUSS_QUESTION_TYPE) . " and b6.`published` = 1),";
                $query .= " a.`has_polls` = (select count(1) from `#__discuss_polls` as b7 where b7.`post_id` = a.`post_id`),";
                $query .= " a.`vote` = (select count(1) from `#__discuss_votes` as b8 where b8.`post_id` = a.`post_id`)";
                $db->setQuery($query);
                $state = $db->query();


                // step 4: update last user_id
                $query = "update `#__discuss_thread` as a";
                $query .= " inner join `#__discuss_posts` as b";
                $query .= "  on a.`id` = b.`thread_id` and b.`id` = (select max(id) from `#__discuss_posts` as c where c.`thread_id` = a.`id`)";
                // $query .= "  on a.`id` = b.`thread_id` and a.`replied` = b.`created`";
                $query .= "    set a.`last_user_id` = b.`user_id`,";
                $query .= "    a.`last_poster_name` = b.`poster_name`,";
                $query .= "    a.`last_poster_email` = b.`poster_email`";
                $query .= " where b.parent_id > 0";

                $db->setQuery($query);
                $state = $db->query();

            }

        }

        return $state;
    }

}
