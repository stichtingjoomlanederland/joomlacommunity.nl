<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussTags extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $alias = null;
	public $created	= null;
	public $published = null;
	public $user_id	= null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_tags', 'id', $db);
	}

	public function load($id = null, $loadByTitle = false)
	{
		static $loaded = array();

		$sig = $id . $loadByTitle;
		$doBind = true;

		if (!isset($loaded[$sig])) {
			
			if (!$loadByTitle) {

				if (ED::cache()->exists($id, 'tag')) {
					$data = ED::cache()->get($id, 'tag');
					$loaded[$sig] = $data;
				} else {

					parent::load($id);
					$loaded[$sig] = $this;
				}
			} else {

				$db = ED::db();

				$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote($this->_tbl) . ' '
						. 'WHERE ' . $db->nameQuote('title') . '=' . $db->Quote($id);

				$db->setQuery($query);

				$db->setQuery($query);
				$tid = $db->loadResult();

				// Try replacing ':' to '-' since Joomla replaces it
				if (!$tid) {
					$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $this->_tbl . ' '
							. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote(EDJString::str_ireplace(':' , '-' , $id));
					$db->setQuery($query);

					$tid = $db->loadResult();
				}

				parent::load($tid);
				$loaded[$sig] = $this;

				$doBind = false;
			}
		}

		if ($doBind) {
			return parent::bind($loaded[$sig]);
		} else {
			return $this->id;
		}
	}

	public function loadOld($id = null, $loadByTitle = false)
	{
		if (!$loadByTitle) {
			return parent::load($id);
		}

		$db	= ED::db();
		$query = 'SELECT *';
		$query .= ' FROM ' 	. $db->nameQuote('#__discuss_tags');
		$query .= ' WHERE (' 	. $db->nameQuote('title') . ' = ' .  $db->Quote(EDJString::str_ireplace(':', '-', $id));
		$query .= ' OR ' 	. $db->nameQuote('alias') . ' = ' .  $db->Quote(EDJString::str_ireplace(':', '-', $id)) . ')';
		$query .= ' LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadObject();

		// Fixed if the the alias was translated
		if (!$result) {
			$db->setQuery('SELECT * FROM `#__discuss_tags`');
			$tags = $db->loadObjectList();

			foreach ($tags as $tag) {
				
				$tagAlias = ED::permalinkSlug($tag->alias);
				$hasMatched = $id == $tagAlias; 

				if ($hasMatched) {
					return parent::load($tag->id);
				}
			}
		}

		$this->id = $result->id;
		$this->title = $result->title;
		$this->alias = $result->alias;
		$this->created = $result->created;
		$this->published = $result->published;
		$this->user_id = $result->user_id;

		return true;
	}

	public function aliasExists($alias = '')
	{
		$db = ED::db();

		$alias = $alias ? $alias : $this->alias;

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_tags');
		$query .= ' WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($alias);

		if ($this->id) {
			$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($this->id);
		}

		$db->setQuery($query);
		$result	= $db->loadResult() > 0 ? true : false;


		return $result;
	}

	public function exists($title)
	{
		$db	= ED::db();

		$query = 'SELECT COUNT(1)';
		$query .= ' FROM ' 	. $db->nameQuote('#__discuss_tags');
		$query .= ' WHERE ' . $db->nameQuote('title') . ' = ' . $db->quote($title);
		$query .= ' LIMIT 1';

		$db->setQuery($query);
		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
	}

	public function bind($data, $ignore = array(), $generateAlias = false)
	{
		parent::bind($data);

		if (!$this->created) {
			$this->created = ED::date()->toSql();
		}

		if ($generateAlias) {

			$alias = $this->alias ? $this->alias : $this->title;
			$alias = ED::permalinkSlug($alias);

			$tmp = $alias;
			$i = 1;

			while ($this->aliasExists($tmp, $this->id) || !$tmp) {
				$alias = !$alias ? ED::permalinkSlug($this->title) : $alias;
				$tmp = !$tmp ? ED::permalinkSlug($this->title) : $alias . '-' . $i;
				$i++;
			}

			$this->alias = $tmp;
		}
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	public function delete($pk = null)
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->nameQuote('#__discuss_posts_tags');
		$query .= ' WHERE ' . $db->nameQuote('tag_id') . '=' . $db->Quote($this->id);
		
		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count > 0) {
			$this->deletePostTag();
		}
		
		$state = parent::delete();

		if ($state) {
			$actionlog = ED::actionlog();
			$actionlog->log('COM_ED_ACTIONLOGS_DELETED_TAG', 'tag', array(
				'tagTitle' => JText::_($this->title)
			));
		}

		return $state;
	}

	public function deletePostTag()
	{
		$db	= ED::db();

		$query = 'DELETE FROM ' . $db->nameQuote('#__discuss_posts_tags');
		$query .= ' WHERE ' . $db->nameQuote('tag_id') . '=' . $db->Quote($this->id);

		$db->setQuery($query);
		$state = $db->query();

		if (!$state) {
			return false;
		}

		return true;
	}
}
