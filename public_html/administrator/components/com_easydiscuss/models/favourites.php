<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelFavourites extends EasyDiscussAdminModel
{
	public function __construct()
	{
		parent::__construct();

		$limit			= $this->app->getUserStateFromRequest( 'com_easydiscuss.categories.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function isFav( $postId, $userId, $type = 'post' )
	{
		$db = DiscussHelper::getDBO();
		$query = 'SELECT ' . $db->nameQuote( 'id' )
				. ' FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote( 'created_by' ) . ' = ' . $db->quote( $userId )
				. ' AND ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->quote( $postId )
				. ' AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->quote( $type );

		$db->setQuery( $query );
		$result = $db->loadResultArray();

		return ( empty( $result ) ? false : true );
	}

	/**
     * Add favourite for single user at specific post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function addFav($postId, $userId, $type = 'post')
	{
		$date = ED::date();
		$fav = ED::table('Favourites');

		$fav->created_by = $userId;
		$fav->post_id = $postId;
		$fav->type = $type;
		$fav->created = $date->toMySQL();

		if (!$fav->store()) {
			return false;
		}

		return true;
	}

	/**
     * Remove favourite for single user at specific post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function removeFav( $postId, $userId, $type = 'post' )
	{
		$db = $this->db;
		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_favourites' )
				. ' WHERE ' . $db->nameQuote( 'created_by' ) . '=' . $db->quote( $userId )
				. ' AND ' . $db->nameQuote( 'post_id' ) . '=' . $db->quote( $postId )
				. ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->quote( $type );

		$db->setQuery($query);

		if(!$db->query()){
			return false;
		}

		return true;
	}

	/**
	 * Retrieve favourite count.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getFavouritesCount($id , $type = 'post')
	{
		$db = ED::db();
		
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_favourites');
		$query[] = 'WHERE ' . $db->nameQuote('post_id') . '=' . $db->Quote($id);
		$query[] = 'AND ' . $db->nameQuote('type') . '=' . $db->Quote($type);
		$query = implode(' ' , $query);

		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	/**
     * Delete any favourite for the post
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function deleteAllFavourites($id)
	{
		if (!$id) {
			return false;
		}

		$query = 'DELETE FROM ' . $this->db->nameQuote('#__discuss_favourites')
				. ' WHERE ' . $this->db->nameQuote('post_id') . '=' . $this->db->Quote($id);

		$this->db->setQuery($query);
		$state = $this->db->query();

		if (!$state) {
			return false;
		}

		return true;
	}

    /**
     * Retrieve post favourites
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getPostFavourites($contentId, $type)
    {
        $db = ED::db();
        
        $displayFormat = $this->config->get('layout_nameformat');
        $displayName = '';

        switch($displayFormat){
            case "name" :
                $displayName = 'a.name';
                break;
            case "username" :
                $displayName = 'a.username';
                break;

            case "nickname" :
            default :
                $displayName = 'IF(c.`nickname` != \'\', c.`nickname`, a.`name`)';
                break;
        }

        $query = 'SELECT a.`id` as `user_id`, b.`id`, ' . $displayName . ' AS `displayname`';
        $query .= ' FROM ' . $db->nameQuote('#__discuss_favourites') . ' AS b';
        $query .= ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS a';
        $query .= '    on b.`created_by` = a.`id`';
        $query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' AS c';
        $query .= '    on b.`created_by` = c.`id`';
        $query .= ' WHERE b.`type` = '. $db->Quote($type);
        $query .= ' AND b.`post_id` = ' . $db->Quote($contentId);
        $query .= ' ORDER BY b.`id` DESC';

        $db->setQuery($query);

        $list = $db->loadObjectList();

        return $list;
    }

    /**
     * Retrieve post favourites
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function updatePostFav($contentId, $increment = true)
    {
        $operator = ($increment) ? '+' : '-';

        // Now update the post
        $db = ED::db();
        $query = 'UPDATE `#__discuss_posts` SET `num_fav` = `num_fav` ' . $operator . ' 1';
        $query .= ' WHERE `id` = ' . $db->Quote($contentId);
        
        $db->setQuery($query);
        $db->query();

        // TODO: We need to update thread table as well
        
    }

}
