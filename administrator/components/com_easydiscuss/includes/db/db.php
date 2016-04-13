<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussDb
{
	public $db 		= null;

	public function __construct()
	{
		$this->db = JFactory::getDBO();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote($str)
	{
		return $this->qn($str);
	}

	public function getEscaped($text, $extra = false)
	{
		return $this->db->escape($text, $extra);
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}


	/**
     * Alias for quote.
     *
     * @access public
     */
    public function q($item, $escape = true)
    {
        return $this->quote($item, $escape);
    }

    /**
     * Alias for quotename.
     *
     * @access public
     */
    public function qn($name, $as = null)
    {
        return $this->quoteName($name, $as);
    }

    /**
     * helper files to add Quote into value from an array
     *
     * @access public
     */
    public function implode($values)
    {
        $str = '';

        foreach($values as $val) {
            $str .= ($str) ? ',' . $this->Quote($val) : $this->Quote($val);
        }

        return $str;
    }

	/**
	 * Synchronizes the database for easydiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public static function sync($from = '')
    {
        $db = ED::db();

        // List down files within the updates folder
        $path = DISCUSS_ADMIN_ROOT . '/updates';

        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $scripts= array();

        if ($from) {
            $folders = JFolder::folders($path);

            if ($folders) {

                foreach ($folders as $folder) {

                    // Because versions always increments, we don't need to worry about smaller than (<) versions.
                    // As long as the folder is greater than the installed version, we run updates on the folder.
                    // We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
                    // We want > $from, NOT >= $from

                    if (version_compare($folder, $from) === 1) {
                        $fullPath = $path . '/' . $folder;

                        // Get a list of sql files to execute
                        $files = JFolder::files( $fullPath , '.json$' , false , true );

                        foreach ($files as $file) {
                            $data = json_decode(JFile::read($file));
                            $scripts    = array_merge($scripts, $data);
                        }
                    }
                }
            }
        } else {

            $files = JFolder::files($path, '.json$', true, true);

            // If there is nothing to process, skip this
            if (!$files) {
                return false;
            }

            foreach ($files as $file) {
                $data = json_decode(JFile::read($file));
                $scripts = array_merge($scripts, $data);
            }
        }

        if (!$scripts) {
            return false;
        }

        $tables = array();
        $indexes = array();
        $affected = 0;


        foreach ($scripts as $script) {

            $columnExist = true;
            $indexExist = true;

            if (isset($script->column)) {

                // Store the list of tables that needs to be queried
                if (!isset($tables[$script->table])) {
                    $tables[$script->table] = $db->getTableColumns($script->table);
                }

                // Check if the column is in the fields or not
                $columnExist = in_array($script->column, $tables[$script->table]);
            }

            if (isset($script->index)) {

                // Get the list of indexes on a table
                if (!isset($indexes[$script->table])) {
                    $indexes[$script->table] = $db->getTableIndexes($script->table);
                }

                $indexExist = in_array($script->index, $indexes[$script->table]);
            }

            if (!$columnExist || !$indexExist) {
                $db->setQuery($script->query);
                $db->Query();

                $affected   += 1;
            }
        }

        return $affected;
    }

    /**
     * Retrieve table columns
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTableColumns($tableName)
    {
        $db = JFactory::getDBO();

        $query  = 'SHOW FIELDS FROM ' . $db->quoteName($tableName);

        $db->setQuery($query);

        $rows = $db->loadObjectList();
        $fields = array();

        foreach ($rows as $row) {
            $fields[] = $row->Field;
        }

        return $fields;
    }

    /**
     * Retrieves table indexes from a specific table.
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
    public static function getTableIndexes($tableName)
    {
        $db = JFactory::getDBO();

        $query = 'SHOW INDEX FROM ' . $db->quoteName($tableName);

        $db->setQuery($query);

        $result = $db->loadObjectList();

        $indexes = array();

        foreach ($result as $row) {
            $indexes[] = $row->Key_name;
        }

        return $indexes;
    }

}
