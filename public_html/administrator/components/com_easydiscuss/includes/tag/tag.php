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

class EasyDiscussTag extends EasyDiscuss
{
	protected $table = null;

	// This is the binded data
	protected $bindData = array();

	public function __construct($item)
	{
		parent::__construct();

		$this->table = ED::table('Tags');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussTags)) {
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussTags) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table->load($item);
		}
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Allows caller to set properties to the table without directly accessing it
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function set($key, $value)
	{
		$this->table->$key = $value;
	}

	/**
	 * Allows caller to bind properties to the table without directly accessing it
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function bind($data)
	{
		$this->table->bind($data);

		$this->bindData = $data;
	}

	/**
	 * Attaches rss links for the tag
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function attachRssLinks()
	{
		$doc = JFactory::getDocument();

		$doc->addHeadLink($this->getFeedLink(), 'alternate', 'rel', [
			'type' => 'application/rss+xml',
			'title' => 'RSS 2.0'
		]);

		$doc->addHeadLink($this->getAtomLink(), 'alternate', 'rel', [
			'type' => 'application/atom+xml',
			'title' => 'Atom 1.0'
		]);
	}

	/**
	 * Generates the RSS permalink to a tag
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getAtomLink()
	{
		return $this->getRssPermalink('atom');
	}

	/**
	 * Generates the RSS permalink to a tag
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getFeedLink($type = 'feed')
	{
		return $this->getRssPermalink('feed');
	}

	/**
	 * Generates the RSS permalink to a tag
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getRssPermalink($type = 'feed')
	{
		static $cache = [];

		$key = $this->table->id . $type;

		if (!isset($cache[$key])) {
			$jConfig = ED::jConfig();
			$joiner = $jConfig->getValue('sef') ? '?' : '&'; 

			if ($type == 'feed') {
				$options = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
				$cache[$key] = EDR::getTagRoute($this->table->id) . $joiner . 'format=feed&type=rss';
			}

			if ($type == 'atom') {
				$options = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
				$cache[$key] = EDR::getTagRoute($this->table->id) . $joiner . 'format=feed&type=atom';
			}
		}

		return $cache[$key];
	}

	/**
	 * Retrieves the title of the tag
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getTitle()
	{
		$title = JText::_($this->table->title);

		return $title;
	}
}
