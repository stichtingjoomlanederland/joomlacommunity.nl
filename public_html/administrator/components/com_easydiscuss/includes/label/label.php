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

class EasyDiscussLabel
{
	protected $table = null;

	// This is the binded data
	protected $bindData = array();

	public function __construct($item)
	{
		$this->table = ED::table('Labels');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussLabels)) {
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussLabels) {
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
	 * @since   5.0.0
	 * @access  public
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
	 * Retrieves the title of the label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getTitle()
	{
		$title = JText::_($this->table->title);

		return $title;
	}

	/**
	 * Retrieves the colour of the label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getColour()
	{
		return $this->table->colour;
	}
}
