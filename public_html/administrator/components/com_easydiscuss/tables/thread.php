<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Import main table.
ED::import( 'admin:/tables/table' );

class DiscussThread extends EasyDiscussTable
{
    public $id = null;
    public $title = null;
    public $alias = null;
    public $created = null;
    public $modified = null;
    public $replied = null;
    public $user_id = null;
    public $post_id = null;
    public $user_type = null;
    public $poster_name = null;
    public $poster_email = null;
    public $last_user_id = null;
    public $last_poster_name = null;
    public $last_poster_email = null;
    public $content = null;
    public $preview = null;
    public $published = null;
    public $category_id = null;
    public $ordering = null;
    public $vote = null;
    public $sum_totalvote = null;
    public $hits = null;
    public $islock = null;
    public $locdate = null;
    public $featured = null;
    public $isresolve = null;
    public $isreport = null;
    public $answered = null;
    public $params = null;
    public $password = null;
    public $legacy = null;
    public $address = null;
    public $latitude = null;
    public $longitude = null;
    public $content_type = null;
    public $post_status = null;
    public $post_type = null;
    public $private = null;
    public $num_likes = null;
    public $num_replies = null;
    public $num_negvote = null;
    public $num_fav = null;
    public $num_attachments = null;
    public $has_polls = null;
    public $cluster_id = null;
    public $cluster_type = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_thread' , 'id' , $db );
	}

	public function updatePostThreadId($postId)
	{
		$db = ED::db();

		// we need to manually run the update statement to avoid possible sql error for unknown column on post table.
		$query = "update " . $db->nameQuote('#__discuss_posts') . " set `thread_id` = " . $db->Quote($this->id);
		$query .= " where `id` = " . $db->Quote($postId);

		$db->setQuery($query);
		$db->query();
	}

    public function getThreadId($postId)
    {
        $db = ED::db();

        $query = "SELECT `id` FROM " . $db->nameQuote('#__discuss_thread') . ' '
                . "WHERE `post_id` = " . $db->Quote($postId);

        $db->setQuery($query);

        $result = $db->loadObject();
        
        if (!$result) {
            return false;
        }

        return $result->id;
    }

    /**
     * Determine this post whether got any accepted reply as answer
     *
     * @since   4.0.19
     * @access  public
     */
    public function getThreadAnswered($id)
    {
        $db = ED::db();

        $query  = "SELECT `answered` FROM " . $db->nameQuote('#__discuss_thread');
        $query .= " WHERE `post_id` = " . $db->Quote($id);

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result->answered >= 1 ? true : false;
    }

}
