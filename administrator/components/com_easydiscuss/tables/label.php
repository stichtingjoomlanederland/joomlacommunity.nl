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

class DiscussLabel extends JTable
{
	public $id			= null;
	public $title		= null;
	public $description	= null;
	public $published	= null;
	public $ordering	= null;
	public $created		= null;
	public $creator		= null;

	private $_alias		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_posts_labels' , 'id' , $db );
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	public function bind( $data , $ignore = array() )
	{
		parent::bind( $data );

		if( empty( $this->created ) )
		{
			$this->created = DiscussHelper::getDate()->toMySQL();
		}

		if( empty( $this->creator ) )
		{
			$this->creator = JFactory::getUser()->id;
		}
	}

	public function move( $direction, $where = '')
	{
		$db = DiscussHelper::getDBO();

		if( $direction == -1) //moving up
		{
			if( $this->ordering > 0 )
			{
				$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` - 5 WHERE `ordering` <= ' . $db->quote( $this->ordering - 2 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` - 1 WHERE `ordering` = ' . $db->quote( $this->ordering - 1 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` - 3 WHERE `id` = ' . $db->quote($this->id);
				$db->setQuery($query);
				$db->query();
			}
		}
		else
		{
			$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` + 5 WHERE `ordering` >= ' . $db->quote( $this->ordering + 2 );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` + 1 WHERE `ordering` = ' . $db->quote( $this->ordering + 1 );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = `ordering` + 3 WHERE `id` = ' . $db->quote($this->id);
			$db->setQuery($query);
			$db->query();
		}

		return $this->rebuild();
	}

	public function rebuildOrdering()
	{
		// Get the input
		$pks	= JRequest::getVar('cid', null, 'post', 'array');
		$order	= JRequest::getVar('order', null, 'post', 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		if (is_array($pks) && is_array($order) && count($pks) == count($order))
		{
			$db = DiscussHelper::getDBO();

			for ($i = 0, $count = count($pks); $i < $count; $i++)
			{
				$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = ' . $order[$i] . ' WHERE `id` = ' . $pks[$i];
				$db->setQuery($query);

				if( !$db->query() )
				{
					return false;
				}
			}

			return $this->rebuild();
		}
		else
		{
			return false;
		}
	}

	public function rebuild()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT id, ordering FROM `#__discuss_posts_labels` ORDER BY ordering, id DESC';
		$db->setQuery($query);
		$rows	= $db->loadObjectList();

		foreach ($rows as $i => $row)
		{
			$order	= $i + 1;
			$query	= 'UPDATE `#__discuss_posts_labels` SET `ordering` = ' . $order . ' WHERE `id` = ' . $row->id;
			$db->setQuery($query);
			if( !$db->query() )
			{
				return false;
			}
		}

		return true;
	}

	public function getTitle( $groups = array() )
	{
		return $this->title;
	}

	public function getAlias()
	{
		if( !isset($this->_alias) )
		{
			$lang	= JFactory::getLanguage();
			$alias	= $lang->transliterate($this->title);

			$alias	= trim(strtolower( JString::str_ireplace(' ', '', $alias) ));
			$alias	= preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $alias);
			$alias	= trim($alias, '-');

			$this->_alias = $alias;
		}

		return $this->_alias;
	}
}
