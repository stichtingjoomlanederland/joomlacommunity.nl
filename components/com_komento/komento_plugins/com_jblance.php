<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/abstract.php');

// Define the paths
include_once(JPATH_ADMINISTRATOR.'/components/com_jblance/helpers/jblance.php');	//include this helper file to make the class accessible in all other PHP files
include_once(JPATH_ADMINISTRATOR.'/components/com_jblance/helpers/link.php');	//include this helper file to make the class accessible in all other PHP files

class KomentoComjblance extends KomentoExtension
{
	public $_item;
	public $_map = array(
						'id'			=> 'id',
						'title'			=> 'project_title',
						'created_by'	=> 'publisher_userid',
						'catid'			=> 'id_category'
						);

	public function __construct($component)
	{
		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = array();
		
		$config = JblanceHelper::getConfig();

		if (!isset($instances[$cid])) {

			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jblance/tables');
			$project = JTable::getInstance('Project', 'Table');
			$state = $project->load($cid);

			if (!$state) {
				return $this->onLoadArticleError($cid);
			}

			$instances[$cid] = $project;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	/**
	 * Retrieves a list of item ids from specific categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getContentIds($categories = '')
	{
		// $db = Komento::getDBO();
		// $query = '';

		// if (!$categories) {
		// 	$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' ORDER BY `virtuemart_product_id`';
		// } else {

		// 	if (is_array($categories)) {
		// 		$categories = implode(',', $categories);
		// 	}

		// 	$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' WHERE `virtuemart_category_id` IN (' . $categories . ') ORDER BY `virtuemart_product_id`';
		// }

		// $db->setQuery($query);
		// return $db->loadResultArray();
	}

	/**
	 * Retrieves a list of categories from JBLance
	 *
	 * @since	1.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCategories()
	{
		// TODO: retrieve categories from JBLANCE
		return true;
	}



	public function isListingView()
	{
		$views = array('showmyproject', 'listproject');

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('layout') == 'detailproject';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		// introtext, text, excerpt, intro, content
		if( $view == 'listing' )
		{
			return $html;
		}

		if( $view == 'entry' )
		{
			if( Komento::joomlaVersion() == '1.5' )
			{
				$article->text .= $html;
			}
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onJBlanceCommentDisplay';
	}

	public function getAuthorId()
	{
		return $this->_item->publisher_userid ? $this->_item->publisher_userid : '';
	}

	public function getCategoryId()
	{
		// TODO: Get Category ID
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		if( !is_object($article) || !property_exists($article, $this->_map['id']) )
		{
			return false;
		}

		return true;
	}

	public function getContentPermalink()
	{
		
		$link = 'index.php?option=com_jblance&view=project&layout=detailproject&id='.$this->_item->id;

		$link = $this->prepareLink( $link );

		return $link;
	}
}
