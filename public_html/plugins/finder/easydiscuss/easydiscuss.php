<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class plgFinderEasyDiscuss extends FinderIndexerAdapter
{
	protected $context = 'EasyDiscuss';
	protected $extension = 'com_easydiscuss';
	protected $layout = 'post';
	protected $type_title = 'EasyDiscuss';
	protected $table = '#__discuss_posts';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();

		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'com_easydiscuss.post') {
			$id = $table->id;

			if (!$table->parent_id) {

				// Delete all replies too
				$model = ED::model('Posts');
				$model->deleteRepliesInFinder($table->id);
			}

		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}

		$state = $this->remove($id);

		return $state;
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew)
	{
		// Only handle easydiscuss items
		if ($context != 'com_easydiscuss.post') {
			return true;
		}

		// Reindex the item
		$this->reindex($row->id);

		return true;
	}

	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param   FinderIndexerResult  $item    The item to index as an FinderIndexerResult object.
	 * @param   string               $format  The item format
	 *
	 * @return  void
	 *
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false) {
			return;
		}

		// Build the necessary route and path information.
		$item->url = '';

		if ($item->parent_id) {
			$item->url = 'index.php?option=com_easydiscuss&view=post&id='. $item->parent_id . '#reply-' . $item->id;

			// use parent post title as title in reply. #108
			$model = ED::model('Posts');

			$title = $model->getPostTitle($item->parent_id);
			$title = JText::_('COM_EASYDISCUSS_SEARCH_REPLY_TITLE_PREFIX') . $title;

			$item->title = $title;

		} else {
			$item->url = 'index.php?option=com_easydiscuss&view=post&id='. $item->id;
		}

		// $item->route	= $item->url;
		$item->route = EDR::_($item->url, true, null, false);
		$item->route = $this->removeAdminSegment($item->route);

		$item->path = FinderIndexerHelper::getContentPath($item->route);


		// Map easydiscuss post privacy into joomla access
		// if( empty( $item->private ) )
		// {
		// 	$item->access	= '1';
		// }
		// else
		// {
		// 	$item->access	= '2';
		// }

		$item->content = $item->preview;

		//$post->content	= JString::substr( strip_tags( $item->content ), 0, 300 );
		$item->content = strip_tags($item->content);

		// if the post is pasword protected, dont show the summary.
		if (!empty($item->password)) {
			$item->summary = JText::_('PLG_FINDER_EASYDISCUSS_PASSWORD_PROTECTED');
		} else {
			$item->summary = $item->content;
		}

		$item->body = $item->content;

		// Add the meta-author.
		$item->metaauthor = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;
		$item->author = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'EasyDiscuss');

		// Add the author taxonomy data.
		if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
		}

		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		if (empty($item->language)) {
			$item->language = '*';
		}

		$item->addTaxonomy('Language', $item->language);

		//lets try to get image
		$editor = ED::getEditorType('question');

		if ($editor == 'html') {
			// @rule: Match images from content
			$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		} else {
			$pattern = '/\[img\](.*?)\[\/img\]/ims';
		}

		preg_match($pattern, $item->body, $matches);

		$image = '';

		if ($matches) {
			$image = isset($matches[1]) ? $matches[1] : '';

			if (JString::stristr($matches[1], 'https://') === false && JString::stristr($matches[1], 'http://') === false && !empty($image)) {
				$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
			}
		}

		if (!$image) {
			$image = rtrim( JURI::root() , '/' ) . '/media/com_easydiscuss/images/default_facebook.png';
		}

		$registry = new JRegistry();
		$registry->set('image', $image);

		$item->params = $registry;

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		if (ED::getJoomlaVersion() >= '3.0') {
			$this->indexer->index($item);
		} else {
			FinderIndexer::index($item);
		}
	}

	private function removeAdminSegment($url = '')
	{
		if ($url) {
			$url = ltrim($url , '/');
			$url = str_replace('administrator/', '', $url);
		}

		return $url;
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	protected function setup()
	{
		$engine = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($engine)) {
			return false;
		}

		require_once($engine);

		jimport('joomla.filesystem.file');

		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $sql  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select( 'a.*, b.title AS category, u.name AS author, eu.nickname AS created_by_alias');

		$sql->select('1 AS access');
		$sql->select('a.published AS state,a.id AS ordering');
		$sql->select('b.published AS cat_state, 1 AS cat_access');
		$sql->from('#__discuss_posts AS a');
		$sql->join('LEFT', '#__discuss_category AS b ON b.id = a.category_id');
		$sql->join('LEFT', '#__users AS u ON u.id = a.user_id');
		$sql->join('LEFT', '#__discuss_users AS eu ON eu.id = a.user_id');

		return $sql;
	}
}
