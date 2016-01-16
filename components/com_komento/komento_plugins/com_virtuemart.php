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
defined('JPATH_VM_ADMINISTRATOR') or define('JPATH_VM_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_virtuemart');
defined('VMPATH_ADMIN') or define('VMPATH_ADMIN', JPATH_VM_ADMINISTRATOR);

// Include vm dependencies
require_once(JPATH_VM_ADMINISTRATOR . '/helpers/config.php');

class KomentoComvirtuemart extends KomentoExtension
{
	public $_item;
	public $_map = array(
						'id'			=> 'virtuemart_product_id',
						'title'			=> 'product_name',
						'hits'			=> 'hits',
						'created_by'	=> 'created_by',
						'catid'			=> 'catid'
						);

	public function __construct($component)
	{
		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = array();
		
		VmConfig::loadConfig();

		if (!isset($instances[$cid])) {

			JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . '/tables');
			$product = JTable::getInstance('Products', 'Table');
			$state = $product->load($cid);

			if (!$state) {
				return $this->onLoadArticleError($cid);
			}

			$instances[$cid] = $product;
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
		$db = Komento::getDBO();
		$query = '';

		if (!$categories) {
			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' ORDER BY `virtuemart_product_id`';
		} else {

			if (is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `virtuemart_product_id` FROM ' . $db->nameQuote( '#__virtuemart_product_categories' ) . ' WHERE `virtuemart_category_id` IN (' . $categories . ') ORDER BY `virtuemart_product_id`';
		}

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	/**
	 * Retrieves a list of categories from virtuemart
	 *
	 * @since	1.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCategories()
	{
		// Load virtuemart's config
		VmConfig::loadConfig();

		$activeLang = VmConfig::$vmlang;
		$db = Komento::getDBO();
		$query = 'SELECT c.`virtuemart_category_id` AS id, l.`category_name` AS title, cx.`category_parent_id` AS parent_id,'
				. ' l.`category_name` AS name, cx.`category_parent_id` AS parent'
				. ' FROM `#__virtuemart_categories_' . $activeLang .'` as l'
				. ' JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)'
				. ' LEFT JOIN `#__virtuemart_category_categories` AS cx ON l.`virtuemart_category_id` = cx.`category_child_id`'
				. ' ORDER BY c.`ordering`';
		$db->setQuery($query);
		$categories	= $db->loadObjectList();

		$children = array();

		foreach ($categories as $row) {
			$parent = $row->parent_id;
			$list = @$children[$parent] ? $children[$parent] : array();
			$list[] = $row;
			$children[$parent] = $list;
		}

		$categories = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		return $categories;
	}

	public function isListingView()
	{
		$views = array('virtuemart', 'category');

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'productdetails';
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
		$entryTrigger = ( Komento::joomlaVersion() > '1.5' ) ? 'onContentAfterDisplay' : 'onPrepareContent';

		return $entryTrigger;
	}

	public function getAuthorId()
	{
		return $this->_item->created_by ? $this->_item->created_by : $this->_item->modified_by;
	}

	public function getCategoryId()
	{
		$db	= Komento::getDBO();
		$query	= 'SELECT `virtuemart_category_id` FROM `#__virtuemart_product_categories` WHERE `virtuemart_product_id` = ' . $db->quote( $this->getContentId() );
		$db->setQuery( $query );

		$productCategory = $db->loadResult();

		return $productCategory;
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
		$productCategory = $this->getCategoryId();

		$productCategory ? $productCategory : JRequest::getInt( 'virtuemart_category_id', 0 );

		$link	= 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$this->_item->virtuemart_product_id.'&virtuemart_category_id='.$productCategory;

		$link = $this->prepareLink( $link );

		return $link;
	}
}
