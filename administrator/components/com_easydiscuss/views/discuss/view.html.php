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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewDiscuss extends EasyDiscussAdminView
{
	public function display( $tpl = null )
	{
		$task	= JRequest::getCmd('task');

		// Set the panel title
		$this->setPanelTitle( JText::_( 'COM_EASYDISCUSS_DASHBOARD' ) );

		$categoryModel 	= $this->getModel( 'Categories' );
		$rows 			= $categoryModel->getAllCategories();
		$categories 	= array();

		foreach( $rows as &$row )
		{
			$category 	= DiscussHelper::getTable( 'Category' );
			$category->load( $row->id );

			$categories[]	= $category;
		}

		$this->assign( 'categories' , $categories );

		$config	= DiscussHelper::getConfig();
		$this->assign( 'config', $config );

		$this->addPathway( 'Home' , '' );
		$this->setLayout('default');
		parent::display( $tpl );
	}

	/**
	 * Method to add a shortcut button the control panel
	 */
	public function addButton( $link, $image, $text, $description = '' )
	{
		ob_start();
	?>
		<li>
			<a href="<?php echo $link;?>">
				<?php echo JHTML::_('image', 'administrator/components/com_easydiscuss/themes/default/images/'.$image, $text );?>
				<span class="item-title"><?php echo $text;?></span>
				<span class="item-description">
					<i class="tipsArrow"></i>
					<div class="tipsBody"><?php echo $description;?></div>
				</span>
			</a>
		</li>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}


	public function getTotalPosts()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote('0');
		$db->setQuery( $query );
		return $db->loadResult();
	}


	public function getTotalReplies()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' != ' . $db->Quote('0');
		$db->setQuery( $query );
		return $db->loadResult();
	}


	public function getTotalSolved()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote('0') . ' AND ' . $db->nameQuote('isresolve') . ' = ' . $db->Quote('1');
		$db->setQuery( $query );
		return $db->loadResult();
	}

	public function getTotalTags()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_tag`';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	public function getTotalCategories()
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_categories`';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	public function getRecentNews()
	{
		return DiscussHelper::getRecentNews();
	}

	public function registerToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'EasyDiscuss' ), 'home');

		$clearCacheIcon = 'delete';

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$my = JFactory::getUser();
			if ($my->authorise('core.admin', 'com_easydiscuss'))
			{
				JToolBarHelper::preferences('com_easydiscuss');
			}

			$clearCacheIcon = 'refresh';
		}
		JToolBarHelper::custom( 'clearCache', $clearCacheIcon, '', JText::_( 'COM_EASYDISCUSS_CLEAR_CACHE' ), false);
	}
}
