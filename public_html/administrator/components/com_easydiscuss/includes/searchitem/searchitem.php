<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussSearchItem extends EasyDiscuss
{
	private $_object = null;
	private $_query = null;

	public $noofdays = null;
	public $daydiff = null;
	public $timediff = null;
	public $itemtype = null;
	public $id = null;

	public $title = null;
	public $content = null;
	public $preview = null;
	public $user_id = null;
	public $category_id = null;
	public $parent_id = null;
	public $thread_id = null;

	public $user_type = null;
	public $created = null;
	public $poster_name = null;
	public $category = null;
	public $password = null;

	public $featured = null;
	public $islock = null;
	public $isresolve = null;
	public $lastupdate = null;
	public $legacy = null;

	public $post_type_suffix = null;
	public $post_type_title = null;

	public $user = null;

	// This contains the error message.
	public $_error = null;


	public function __construct($item)
	{
		parent::__construct();

		// lets bind this item
		$this->bind($item);

		$this->user = ED::user($this->user_id);

		if ($this->itemtype == 'posts' || $this->itemtype == 'replies') {
			$this->_object = ED::post($item);
		} else {
			$this->_object = ED::category($this->id);
		}


		$this->_query = $this->input->get('query', '', 'string');

		return $this;
	}

	public function bind($item)
	{
		// for now we just deal with object.

		$arr = get_object_vars($this);

		foreach ($arr as $key => $val) {
			if (isset($item->$key)) {
				$this->$key = $item->$key;
			}
		}
	}

	private function highlight($contenttext, $query)
	{
		$result = preg_replace('#\xE3\x80\x80#s', ' ', $query);
		$search = preg_split("/\s+/u", $result);
		$needle = $search[0];

		$words = array_unique($search);

		$introtext = strip_tags($contenttext);
		$introtext = preg_replace('/\s+/', ' ' , $introtext);
		$pos = strpos($introtext, $needle);

		if ($pos !== false) {
			$text = '...';
			$startpos = ($pos - 10) >= 0 ? $pos - 10 : 0;
			$endpos = ($pos - 10) >= 0 ? 10 : ($pos - $startpos);

			$front = JString::substr($introtext, $startpos, $endpos);

			if (JString::strlen($introtext) > $endpos) {
				$endpos = $pos + JString::strlen($needle);
				$end = JString::substr($introtext, $endpos, 10);

				if (JString::strlen($front) > 0) {
					$text  = $text . $front;
				}

				$text  = $text . $needle;

				if (JString::strlen($end) > 0) {
					$text  = $text . $end . '...';
				}
			} else {
				$text = $front;
			}

			$introtext = $text;
		}


		$pattern = '#(';
		$x = 0;

		foreach ($words as $key => $word) {
			$pattern .= ($x == 0 ? '' : '|');
			$pattern .= preg_quote($word, '#');

			$x++;
		}

		$pattern .= '(?!(?>[^<]*(?:<(?!/?a\b)[^<]*)*)</a>))#iu';

		// Perform highlighting on the introtext
		$content = preg_replace($pattern, '<span class="highlight">\0</span>', $introtext);



		return $content;
	}




	// private function formatItem()
	// {

	//     $query = $this->input->get('query', '', 'string');

	//     $this->content = $this->highlight($this->content, $query);
	// }

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function __get($key)
	{
		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->_object->$key;
	}

	/**
	 * Retrieves the post suffix
	 *
	 * @alternative for ->post_type_suffix
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTypeSuffix()
	{
		return $this->post_type_suffix;
	}

	/**
	 * Retrieve the post type's title
	 *
	 * @alternative for ->post_type_title
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTypeTitle()
	{
		return $this->post_type_title;
	}


	/**
	 * Retrieves the category object for the post
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getCategory()
	{
		$categories = array();

		if (!isset($categories[$this->category_id])) {
			$category = ED::category($this->category_id);

			$categories[$this->category_id] = $category;
		}

		return $categories[$this->category_id];
	}

	/**
	 * Get the item type. posts, replies, category
	 *
	 * @alternative for ->itemtype
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getType()
	{
		if (isset($this->itemtype)) {
			return $this->itemtype;
		}

		return false;
	}


	/**
	 * Get the item type. posts, replies, category
	 *
	 * @alternative for ->itemtype
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getItemTypeTitle()
	{
		if ($this->isCategory()) {
			return JText::_('COM_EASYDISCUSS_SEARCH_ITEM_CATEGORY_TYPE');
		} else if ($this->isQuestion()) {
			return JText::_('COM_EASYDISCUSS_SEARCH_ITEM_POSTS_TYPE');
		} else if ($this->isReply()) {
			return JText::_('COM_EASYDISCUSS_SEARCH_ITEM_REPLIES_TYPE');
		}

		return '';
	}

	/**
	 * Retrieves the duration string of the post
	 *
	 * @alternative for previous ->duration
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getDuration()
	{
		static $duration = array();

		$key = $this->id;

		if (!isset($duration[$key])) {
			$diff = new stdClass();

			$diff->daydiff = $this->daydiff;
			$diff->timediff = $this->timediff;

			$duration[$key] = ED::getDurationString($diff);
		}

		return $duration[$key];
	}

	/**
	 * Generates the permalink for the post
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getPermalink()
	{
		static $links = array();

		$key = $this->id . $this->itemtype;

		if (!isset($links[$key])) {
			$link = '';
			if ($this->isReply()) {
				// $link = EDR::_('view=post&id=' . $this->_object->parent_id);


				// $url = 'view=post&id=' . $question->id . '&limitstart=' . $limitstart . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $this->id;


				// $link = $link . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $this->id;
				$link = EDR::_($this->_object->getReplyPermalink());

			} else {
				$link = $this->_object->getPermalink();
			}

			$links[$key] = $link;
		}

		return $links[$key];
	}

	public function isUnRead()
	{
		$isUnRead = false;

		if (($this->getItemType() == 'posts' || $this->getItemType() == 'replies') && ($this->my->id != 0)) {
			$isUnRead =  ($this->my->isRead($this->id) || $this->legacy) ? false : true;
		}

		return $isUnRead;
	}



	/**
	 * Gets the category title (text only)
	 *
	 * @alternative for ->category
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getCategoryTitle()
	{
		$categoryTitle = JText::_($this->category);

		return $categoryTitle;
	}


	/**
	 * Retrieves the intro text of a post
	 *
	 * @alternative for ->content
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getContent()
	{
		static $_cache = array();

		$key = $this->id . $this->itemtype;

		if (isset($_cache[$key])) {
			return $_cache[$key];
		}

		$content = $this->content;

		if (! $this->isCategory()) {
			$content = $this->_object->getIntro();
		}

		$content = $this->highlight($content, $this->_query);
		$_cache[$key] = $content;

		return $content;
	}

	/**
	 * Retrieves the intro text of a post
	 *
	 * @alternative for ->content
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getTitle()
	{
		static $_cache = array();

		$key = $this->id . $this->itemtype;

		if (isset($_cache[$key])) {
			return $_cache[$key];
		}

		$content = $this->title;
		if (! $this->isCategory()) {
			if ($this->isReply()) {
				$title = $this->_object->getParent()->getTitle();
				$this->title = JText::_('COM_EASYDISCUSS_SEARCH_REPLY_TITLE_PREFIX') . $title;
			}

			$content = ED::badwords()->filter($this->title);
		}

		$_cache[$key] = $content;

		return $content;
	}

	/**
	 * Retrieves the owner of the item
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getOwner()
	{
		static $owners = array();

		$key = $this->user_id;

		if (!isset($owners[$key])) {
			$owners[$key] = ED::user($key);
		}

		return $owners[$key];
	}

	/**
	 * Gets the item author's name
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getAuthorName() {
		$name = '';

		if ($this->user_id) {
			$name = $this->getOwner()->getName();
		} else {
			$name = $this->poster_name;
		}

		return $name;
	}

	/**
	 * Gets the item author's permalink. if yes, it wil just return javascript:void(0).
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getAuthorPermalink() {
		$link = '';

		if ($this->user_id) {
			$link = $this->getOwner()->getPermalink();
		} else {
			$link = 'javascript:void(0);';
		}

		return $link;
	}

	/**
	 * Gets the type of the item
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function getItemType()
	{
		return $this->itemtype;
	}

	/**
	 * Determines if this search item is a category
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isCategory()
	{
		return  $this->getItemType() == 'category';
	}


	/**
	 * Determines if this search item is a question
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isQuestion()
	{
		return  $this->getItemType() == 'posts';
	}

	/**
	 * Determines if this search item is a reply.
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function isReply()
	{
		return  $this->getItemType() == 'replies';
	}

	/**
	 * Determines if the post is password protected.
	 *
	 * @since   4.0
	 * @access  public
	 * @return  boolean True if the post is protected.
	 */
	public function isProtected()
	{
		$isProtected = false;

		if (! $this->isCategory()) {
			$isProtected = $this->_object->isProtected();
		}

		return $isProtected;
	}

	/**
	 * Determines if the item's the suffix.
	 *
	 * @since   4.0
	 * @access  public
	 * @return  boolean string
	 */
	public function getPostTypeSuffix()
	{
		$suffix = $this->post_type_title;
		return $suffix;
	}

	/**
	 * Determines if the post is resolved.
	 *
	 * @since   4.0
	 * @access  public
	 * @return  boolean True if the post is resolved.
	 */
	public function isResolved()
	{
		// If that is reply item, need to check the thread is it resolved or not
		if ($this->isReply()) {
			$thread = ED::post($this->parent_id);
			return $thread->isresolve;
		}
		return $this->isresolve;
	}

	/**
	 * Determines if this item is featured
	 *
	 * @since   4.0
	 * @access  public
	 * @return  boolean True if the post is featured.
	 */
	public function isFeatured() {
		return $this->featured;
	}

	/**
	 * Determines if this item is locked
	 *
	 * @since   4.0
	 * @access  public
	 * @return  boolean True if the item is protected.
	 */
	public function isLocked() {
		return $this->islock;
	}

}
