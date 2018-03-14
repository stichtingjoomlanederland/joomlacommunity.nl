<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('JPATH_BASE') or die;

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

/**
 * Finder adapter for Joomla Tag.
 *
 * @package     Joomla.Plugin
 * @subpackage  Finder.Tags
 * @since       3.1
 */
class PlgFinderRseventspro extends FinderIndexerAdapter
{
	/**
	 * The plugin identifier.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $context = 'Events';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $extension = 'com_rseventspro';

	/**
	 * The sublayout to use when rendering the results.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $layout = 'show';

	/**
	 * The type of content that the adapter indexes.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $type_title = 'Event';

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $table = '#__rseventspro_events';

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * The field the published state is stored in.
	 *
	 * @var    string
	 * @since  3.1
	 */
	protected $state_field = 'published';

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table) {
		if ($context == 'com_rseventspro.event') {
			$id = $table->id;
		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;
		} else {
			return true;
		}
		// Remove the items.
		return $this->remove($id);
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
	 * @since   3.1
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew) {
		// We only want to handle tags here.
		if ($context == 'com_rseventspro.event') {
			// Reindex the item
			$this->reindex($row->id);
		}

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
	 * @since   3.1
	 * @throws  Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html') {
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false) {
			return;
		}
		
		// Build the necessary route and path information.
		$item->url		= $this->getURL($item->id, $this->extension, $this->layout);
		$item->route	= $item->itemid ? 'index.php?option=com_rseventspro&layout=show&id='. rseventsproHelper::sef($item->id,$item->title).'&Itemid='.$item->itemid : RseventsproHelperRoute::getEventRoute($item->id);
		$item->path		= FinderIndexerHelper::getContentPath($item->route);

		// Get the menu title if it exists.
		$title = $this->getItemMenuTitle($item->url);

		// Adjust the title if necessary.
		if (!empty($title) && $this->params->get('use_menu_title', true)) {
			$item->title = $title;
		}

		// Add the meta-author.
		$item->metaauthor = $item->author;

		// Handle the link to the meta-data.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'link');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Events');

		// Add the author taxonomy data.
		if (!empty($item->author)) {
			$item->addTaxonomy('Author',$item->author);
		}
		
		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// Index the item.
		if (rseventsproHelper::isJ3())
			$this->indexer->index($item);
		else 
			FinderIndexer::index($item);
	}

	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.1
	 */
	protected function setup() {
		// Load dependent classes.
		require_once JPATH_SITE . '/components/com_rseventspro/helpers/rseventspro.php';
		require_once JPATH_SITE . '/components/com_rseventspro/helpers/route.php';
		return true;
	}

	/**
	 * Method to get the SQL query used to retrieve the list of content items.
	 *
	 * @param   mixed  $query  A JDatabaseQuery object or null.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   3.1
	 */
	protected function getListQuery($query = null) {
		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$query = $query instanceof JDatabaseQuery ? $query : $db->getQuery(true)
			->select('a.id, a.name AS title, a.description AS summary, a.itemid')
			->select('a.start AS start_date, a.owner AS created_by')
			->select('a.metakeywords AS metakey, a.metadescription AS metadesc')
			->select('1 AS access, a.end AS end_date')
			->select('a.published AS state');

		$query->from('#__rseventspro_events AS a');

		// Join the #__users table
		$query->select('u.name AS author')
			->join('LEFT', '#__users AS u ON u.id = a.owner');

		return $query;
	}
	
	/**
	 * Method to get a SQL query to load the published and access states for
	 * an article and category.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getStateQuery() {
		$db		= JFactory::getDbo();
		$query  = $db->getQuery(true);

		// Item ID
		$query->select('a.id');
		$query->select('a.' . $this->state_field . ' AS state');
		$query->from($this->table . ' AS a');

		return $query;
	}
	
	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param   integer  $id         The id of the item.
	 * @param   string   $extension  The extension the category is in.
	 * @param   string   $view       The view for the URL.
	 *
	 * @return  string  The URL of the item.
	 *
	 * @since   2.5
	 */
	protected function getURL($id, $extension, $view) {
		return 'index.php?option=com_rseventspro&layout=show&id='.$id;
	}
	
	public function onFinderChangeState($context, $pks, $value) {
		// We only want to handle tags here
		if ($context == 'com_rseventspro.event') {
			$this->itemStateChange($pks, $value);
		}
	}
}