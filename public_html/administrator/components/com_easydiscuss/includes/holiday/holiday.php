<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyDiscussHoliday extends EasyDiscuss
{
	// This is the DiscussConversation table
	public $table = null;

	public $message = null;

	public function __construct($item)
	{
		parent::__construct();

		// Always have a default table available.
		$this->table = ED::table('Holidays');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussHolidays)) {
			$this->table->bind($item);
		}

		// If the object is DiscussHolidays, just map the variable back.
		if ($item instanceof DiscussHolidays) {
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
     * @since   4.0
     * @access  public
     * @param   string
     * @return
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
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function set($key, $value)
    {
        $this->table->$key = $value;
    }
    
    /**
     * Deletes an holiday
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function delete()
    {
        // Delete it from the db first
        return $this->table->delete();
    }

    /**
     * Saves an holiday
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function save()
    {
        $this->table->created = ED::date()->toSql();
        return $this->table->store();
    }

     /**
     * Bind an holiday
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function bind($data)
    {
        // Normalize before we bind
        $this->table->start = ED::date($data['endDate'] . ' 00:00:01')->toSql();
        $this->table->end = ED::date($data['endDate'] . ' 23:59:59')->toSql();

        $data['published'] = is_null($data['published']) ? 0 : 1;

        return $this->table->bind($data);
    }
}
