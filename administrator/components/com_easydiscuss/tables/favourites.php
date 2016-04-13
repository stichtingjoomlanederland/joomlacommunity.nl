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

ED::import('admin:/tables/table');

class DiscussFavourites extends EasyDiscussTable
{
	public $id			= null;
	public $created_by	= null;
	public $post_id		= null;
	public $type		= null;
	public $created		= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_favourites' , 'id' , $db );
	}

    /**
     * Return false if the user is already favourite something
     * Else return the existing id
     *
     * @since   4.0
     * @access  public
     * @param   
     * @return  
     */
    public function favExists()
    {
        $db = ED::db();

        $query = 'select `id` from `#__discuss_favourites`';
        $query  .= ' where `type` = ' . $db->Quote($this->type);
        $query  .= ' and `post_id` = ' . $db->Quote($this->post_id);
        $query  .= ' and `created_by` = ' . $db->Quote($this->created_by);

        $db->setQuery($query);
        $result = $db->loadResult();

        return (empty($result)) ? false : $result;
    }

    /**
     * Loads a like object given the post id and the user id.
     *
     * @since   4.0
     * @access  public
     * @param
     * @return 
     */
    public function loadByPost($content_id, $userId)
    {
        $db = ED::db();
        $query  = 'SELECT * FROM ' . $db->nameQuote($this->_tbl) . ' '
                . 'WHERE ' . $db->nameQuote('type') . '=' . $db->Quote('post') . ' '
                . 'AND ' . $db->nameQuote('post_id') . '=' . $db->Quote($content_id) . ' '
                . 'AND ' . $db->nameQuote('created_by') . '=' . $db->Quote($userId);

        $db->setQuery($query);
        $data = $db->loadObject();

        return parent::bind($data);
    }

}
