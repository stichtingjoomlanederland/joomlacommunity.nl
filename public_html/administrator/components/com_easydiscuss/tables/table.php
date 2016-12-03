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

class EasyDiscussTable extends JTable
{
    private $composite = array();

    protected $privacy = null;

    public function __construct($table, $key, $db)
    {
        // Set internal variables.
        $this->_tbl = $table;
        $this->_tbl_key = $key;

        // For Joomla 3.2 onwards
        $this->_tbl_keys = array($key);

        $this->_db = $db;

        // Implement JObservableInterface:
        // Create observer updater and attaches all observers interested by $this class:
        $joomlaVersion = ED::getJoomlaVersion();

        if ($joomlaVersion >= '3.0' && class_exists('JObserverUpdater')) {
            $this->_observers = new JObserverUpdater($this);
            JObserverMapper::attachAllObservers($this);
        }
    }

    /**
     * Tired of fixing conflicts with JTable::getInstance . We'll overload their method here.
     *
     * @param   string  $type    The type (name) of the JTable class to get an instance of.
     * @param   string  $prefix  An optional prefix for the table class name.
     * @param   array   $config  An optional array of configuration values for the JTable object.
     *
     * @return  mixed    A JTable object if found or boolean false if one could not be found.
     *
     * @link    http://docs.joomla.org/JTable/getInstance
     * @since   11.1
     */
    public static function getInstance($type, $prefix = 'Discuss', $config = array())
    {
        // Sanitize and prepare the table class name.
        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
        $tableClass = $prefix . ucfirst($type);

        // Only try to load the class if it doesn't already exist.
        if (!class_exists($tableClass)) {
            // Search for the class file in the JTable include paths.
            $path = dirname(__FILE__) . '/' . strtolower($type) . '.php';

            // Import the class file.
            include_once $path;
        }

        return parent::getInstance($type, $prefix, $config);
    }

    /**
     * Method to reset class properties to the defaults set in the class
     * definition. It will ignore the primary key as well as any private class
     * properties.
     *
     * @return  void
     *
     * @link    http://docs.joomla.org/JTable/reset
     * @since   11.1
     */
    public function reset()
    {
        $properties = get_object_vars($this);
        $columns = array();

        foreach($properties as $key => $value) {
            if ($key != $this->_tbl_key && strpos($key, '_') !== 0) {
                $columns[] = $value;
            }
        }

        return $columns;
    }

    /**
     * Override JTable behavior to perform additional cleanups.
     *
     * @since   4.0
     * @param   null
     * @return  bool    The state of the storing.
     */
    public function store($updateNulls = false)
    {
        // If child class has `created` column, we'll check if it is set.
        if (property_exists($this, 'created') && !$this->created) {
            $this->created  = ED::date()->toSql();
        }

        // Determine the verb for stream items.
        $verb = $this->id ? 'update' : 'create';

        $state = parent::store($updateNulls);

        return $state;
    }

    /**
     * Override JTable::delete to perform additional actions
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function delete($pk = null)
    {
        $state = parent::delete($pk);

        return $state;
    }

    /**
     * Determines whether or not the provided id is the creator of the item.
     * Any subclasses trying to inherit this should have a column called `user_id`.
     *
     * Example:
     * <code>
     * <?php
     * $listId  = JRequest::getInt('listId');
     * $table   = ED::table('List');
     * $table->load($listId);
     * $table->isCreator($my->id);
     * <code>
     *
     * @since   4.0
     * @access  public
     * @param   int     $id     The creator's id.
     * @return  boolean         True if the id provided is a creator, false otherwise.
     *
     */
    public function isCreator($id = null)
    {

        // Subclasses implementing this might not have a `user_id` column.
        if (!property_exists($this, 'user_id')) {
            return false;
        }

        // If argument is null, we need to detect the current logged in user.
        if (is_null($id)) {
            $id = ED::user()->id;
        }

        if ($this->user_id == $id) {
            return true;
        }

        return false;
    }

    /**
     * Sets the current `state` column in the table.
     * Any subclasses trying to inherit this should have a column called `state`.
     *
     * Example:
     * <code>
     * <?php
     * $table   = ED::table('Posts');
     * $table->setState(true);
     * <code>
     *
     * @since   4.0
     * @access  public
     * @param   bool        The state of the item.
     * @return  self
     *
     */
    public function setState($state)
    {
        // Subclasses implementing this might not have a `state` column.
        if (!property_exists($this, 'state')) {
            return false;
        }

        // Here, the `state` column exists.
        $this->state = $state;

        return $this;
    }

    /**
     * Runs some count query to determine if there's any record.
     *
     * @since   4.0
     * @access  public
     * @param   Array   An array of conditions
     * @return
     */
    public function exists($wheres)
    {
        $db = ED::db();

        $query = 'SELECT COUNT(1) FROM ' . $db->nameQuote($this->_tbl) . ' WHERE 1 ';

        foreach ($wheres as $key => $value) {
            $query .= 'AND ' . $db->nameQuote($key) . '=' . $db->Quote($value) . ' ';
        }

        $db->setQuery($query);

        return $db->loadResult() > 0;
    }

    private function changeState($items, $state = DISCUSS_ID_PUBLISHED)
    {
        $db = ED::db();
        $state = (int) $state;

        // Fix the values to avoid anyone from abusing it.
        for ($i = 0; $i < count($items); $i++) {
            $items[$i] = (int) $items[$i];
            $items[$i] = $db->Quote($items[$i]);
        }

        $items = $db->nameQuote($this->_tbl_key) . '=' . implode(' OR ' . $db->nameQuote($this->_tbl_key) . '=', $items);

        $query = 'UPDATE ' . $db->nameQuote($this->_tbl) . ' SET ' . $db->nameQuote('state') . '=' . $db->Quote($state) . ' WHERE (' . $items . ')';

        $db->setQuery($query);

        if (!$db->query()) {
            return false;
        }

        return true;
    }

    public function unpublish($items = array())
    {
        if (empty($items)) {
            $items = array($this->{$this->_tbl_key});
        }

        // Single item.
        if (!is_array($items)) {
            $items = array($items);
        }

        return self::changeState($items, DISCUSS_ID_PUBLISHED);
    }

    /**
     * Publishes a specific item.
     *
     * @since   4.0
     * @access  public
     * @param   Array   An array of items (optional)
     * @param   int     The state to set.
     * @param   int     Not used, just to fix strict standard errors.
     * @return  bool    True if success false otherwise.
     */
    public function publish($items = array(), $state = 1, $userId = 0)
    {
        if (empty($items)) {
            $items  = array($this->{$this->_tbl_key});
        }

        // Ensure that the items is an array.
        $items  = ED::makeArray($items);

        return self::changeState($items, SOCIAL_STATE_PUBLISHED);
    }

    /**
     * Converts a table layer into a JSON encoded string.
     *
     */
    public function toJSON()
    {
        $properties = get_class_vars(get_class($this));
        $result = array();

        foreach ($properties as $key => $value) {
            if ($key[0] != '_') {
                $result[$key] = is_null($this->get($key)) ? '' : $this->get($key);
            }
        }

        return ED::json()->encode($result);
    }


    /**
     * Converts a table layer into an array
     *
     */
    public function toArray()
    {
        $properties = get_class_vars(get_class($this));
        $result = array();

        foreach ($properties as $key => $value) {
            if ($key[0] != '_') {
                $result[$key] = is_null($this->get($key)) ? '' : $this->get($key);
            }
        }
        return $result;
    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * Returns the current table's property translated.
     * @since   4.0
     * @access  public
     * @param   string  The key's title.
     * @return  mixed   The value (translated) in JText.
     */
    public function get($key, $default = '')
    {
        $val = JText::_($this->$key);

        if (empty($val)) {
            return $default;
        }

        return $val;
    }

    /**
     * Returns a translated text from the table column.
     *
     * @since  4.0
     * @access public
     */
    public function _($key, $default = null)
    {
        if (empty($this->$key)) {
            return $default;
        }

        return JText::_($this->$key);
    }

    /**
     * Responsible to output all properties of the table object.
     *
     * @since   4.0
     * @param   null
     * @return  Array   An array of property / values.
     **/
    public function export()
    {
        $obj = new stdClass();
        $properties = get_class_vars(get_class($this));

        foreach ($properties as $key => $value) {
            if ($key[0] != '_') {
                $obj->$key = $this->$key;
            }
        }

        return (array) $obj;
    }

    /**
     * Overwrites JTable's getNextOrder function by expecting an array of columns and values or SocialSql object
     *
     * @since   4.0
     * @access  public
     * @param   string/array  The filter clause to pass to JTable getNextOrder function
     * @return  int
     **/
    public function getNextOrder($where = '')
    {
        $string = '';

        if (is_string($where)) {
            $string = $where;
        }

        if (is_array($where)) {
            $db = ED::db();

            $string = array();

            foreach ($where as $k => $v) {
                $string[] = $db->nameQuote($k) . ' = ' . $db->quote($v);
            }

            $string = implode(' AND ', $string);
        }

        return parent::getNextOrder($string);
    }


    /**
     * Override parent's hit behavior
     *
     * @since   4.0
     * @access  public
     * @return  boolean
     */
    public function addHit()
    {
        $ip = JRequest::getVar('REMOTE_ADDR', '', 'SERVER');

        if (!empty($ip) && !empty($this->id)) {
            $token = md5($ip . $this->id . $this->_tbl);

            $session = JFactory::getSession();
            $exists = $session->get($token, false);

            if ($exists) {
                return true;
            }

            $session->set($token, 1);
        }

        return parent::hit();
    }
}

