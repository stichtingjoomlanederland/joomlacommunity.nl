<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.540
 * @date                2016-07-18
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

class ShlDbClass extends ShlSystem_Abstractdecorator
{

	const CACHE_STATS_DATASET_NAME = 'shl_database';

	// instance unique name
	public $instanceName = '';

	// query cache option
	public $queryCacheEnabled            = true;
	public $queryCacheEnabledForJoomla   = false;
	public $queryCacheTableExclusionList = array('#__sh404sef_urls', '#__extensions', '#__schemas', '#__session', '#__update_categories',
	                                             '#__update_sites', '#__update_sites_extensions', '#__updates');

	// a unique id needed when using the query cache
	public $queryCacheUid = '';

	// flag to help decide if this is a Joomla db
	// request or one that goes through our db helper
	private $_isShLibHelper = false;

	/**
	 * Provides an object with cache usage stats
	 * for the current page load only
	 */
	public function shlGetCacheStats()
	{

		return ShlCache_Manager::getCacheStats(self::CACHE_STATS_DATASET_NAME);
	}

	/**
	 *
	 * Prepare a query, quoting or name quoting some
	 * of its constituents
	 * ?? will be replaced with name quoted data from the $nameQuoted parameter
	 * ? will be replaced with quoted data from the $quoted parameter
	 *
	 * Example:
	 *   $query = 'select ?? from ?? where ?? <> ?'
	 *   with
	 *     $nameQuoted = array( 'id', '#__table', 'counter')
	 *     $quoted = array( 'test')
	 *
	 * will return
	 *
	 *   select `id` from `#__table` where `counter` <> 'test'
	 *
	 *
	 * @param string $query
	 * @param array $nameQuoted
	 * @param array $quoted
	 * @param string $namePlaceHolder
	 * @param string $dataPlaceHolder
	 */
	public function shlQuoteQuery($query, $nameQuoted = array(), $quoted = array(), $namePlaceHolder = '??', $dataPlaceHolder = '?')
	{

		$newQuery = '';

		// name quoting
		if (!empty($nameQuoted))
		{
			// find placeholders
			$sqlBits = explode($namePlaceHolder, $query);
			$i = 0;
			// replace each place holder by the matching value
			foreach ($nameQuoted as $data)
			{
				$newQuery .= $sqlBits[$i];
				$newQuery .= $this->quoteName($data);
				$i += 1;
			}
			if (isset($sqlBits[$i]))
			{
				$newQuery .= $sqlBits[$i];
			}
		}

		if (strpos($newQuery, $namePlaceHolder) !== false)
		{
			throw new ShlDbException(__METHOD__ . ': ' . 'Invalid db query sent to queryQuote helper: ' . $query . '. Maybe missing some data.');
		}

		// name quoting
		if (!empty($quoted))
		{
			// find placeholders
			$sqlBits = explode($dataPlaceHolder, $newQuery);
			$newQuery = '';
			$i = 0;
			// replace each place holder by the matching value
			foreach ($quoted as $data)
			{
				$newQuery .= $sqlBits[$i];
				$newQuery .= $this->_shlPrepareData($data);
				$i += 1;
			}
			if (isset($sqlBits[$i]))
			{
				$newQuery .= $sqlBits[$i];
			}
		}

		return $newQuery;
	}

	/**
	 * Prepare and set a query against the db object
	 *
	 * @param String $table The table name
	 * @param Array $aData An array of field to be inserted in the db ('columnName' => 'columnValue')
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	public function shlSetInsertUpdateQuery($table, $aData, $mWhere = '', $aWhereData = array())
	{

		if ($this->shlIsRecord($table, $mWhere, $aWhereData))
		{
			// update it
			$this->shlSetUpdateQuery($table, $aData, $mWhere, $aWhereData);
		}
		else
		{
			// or insert it
			$this->shlSetInsertQuery($table, $aData);
		}

		return $this;
	}

	/**
	 * Prepare and set a SELECT query against the db
	 *
	 * @param String $table The table name
	 * @param Array $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy , a list of columns to order the results
	 * @param Integer $offset , first line of result set to select
	 * @param Integer $lines , max number of lines to select
	 */
	public function shlSetSelectQuery($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
	                                  $lines = 0)
	{

		// sanitize
		$aColList = empty($aColList) ? array('*') : $aColList;
		$aColList = is_string($aColList) ? array($aColList) : $aColList;

		// which columns to fetch ?
		$quotedColList = array();
		foreach ($aColList as $columnName)
		{
			$quotedColList[] = $columnName == '*' ? '*' : $this->quoteName($columnName);
		}
		$columns = implode(', ', $quotedColList);

		// where to look for
		$where = $this->_shlBuildWhereClause($mWhere, $aWhereData);

		// order by clause
		$orderByClause = $this->_shlBuildOrderByClause($orderBy);

		// lines limit clause
		$limitClause = $this->_shlBuildLimitClause($offset, $lines);

		// set up the query
		$this->setQuery('SELECT ' . $columns . ' FROM ' . $table . $where . $orderByClause . $limitClause . ';');

		return $this;
	} // end of setSelectQuery

	/**
	 * Prepare and set a select/count query against the db
	 *
	 * @param String $table The table name
	 * @param String $column an optional column to be counter
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	public function shlSetCountQuery($table, $column = '*', $mWhere = '', $aWhereData = array())
	{

		// sanitize
		$column = empty($column) || $column == '*' ? '*' : $this->quoteName($column);

		// where to look for
		$where = $this->_shlBuildWhereClause($mWhere, $aWhereData);

		// set up the query
		$this->setQuery('SELECT count(' . $column . ') FROM ' . $table . $where . ';');

		return $this;
	} // end of setSelectQuery

	/**
	 * Prepare and set an UPDATE query against the db
	 *
	 * @param String $table The table name
	 * @param Array $aData array of values pairs ( ie 'columnName' => 'columnValue')
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	public function shlSetUpdateQuery($table, $aData, $mWhere = '', $aWhereData = array())
	{

		// which columns to set ?
		$set = '';
		if (!empty($aData))
		{
			foreach ($aData as $columnName => $columnValue)
			{
				$set .= ', ' . $this->quoteName($columnName) . '=' . $this->_shlPrepareData($columnValue);
			}
			// remove leading ', '
			$set = substr($set, 2);
		}

		// check result
		if (empty($set))
		{
			return false;
		}

		// where to look for
		$where = $this->_shlBuildWhereClause($mWhere, $aWhereData);

		// set up the query
		$this->setQuery('UPDATE ' . $this->quoteName($table) . ' SET ' . $set . $where . ';');

		return $this;
	}

	/**
	 * Prepare and set an INSERT query against the db
	 *
	 * @param String $table The table name
	 * @param Array $aData array of values pairs ( ie 'columnName' => 'columnValue')
	 */
	public function shlSetInsertQuery($table, $aData)
	{

		// which columns to set ?
		$columns = '';
		$values = '';
		if (!empty($aData))
		{
			foreach ($aData as $columnName => $columnValue)
			{
				$columns .= ', ' . $this->quoteName($columnName);
				$values .= ', ' . $this->_shlPrepareData($columnValue);
			}
			// remove leading ', '
			$columns = substr($columns, 2);
			$values = substr($values, 2);
		}

		// set up the query
		$this->setQuery('INSERT INTO ' . $this->quoteName($table) . ' (' . $columns . ') VALUES (' . $values . ');');

		return $this;
	}

	/**
	 * Prepare and set a DELETE query against the db
	 *
	 * @param String $table The table name
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	public function shlSetDeleteQuery($table, $mWhere = '', $aWhereData = array())
	{

		// where to look for
		$where = $this->_shlBuildWhereClause($mWhere, $aWhereData);

		// set up the query
		$this->setQuery('DELETE FROM ' . $this->quoteName($table) . $where . ';');

		return $this;
	}

	/**
	 * Returns true if a record exists matching 'where' condition
	 *
	 * @param String $table , the table to look into
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	public function shlIsRecord($table, $mWhere = '', $aWhereData = array())
	{

		// where to look for
		$where = $this->_shlBuildWhereClause($mWhere, $aWhereData);

		if (empty($where))
		{
			return false;
		}

		// set up the query and load result
		$this->setQuery('SELECT count(*) FROM ' . $this->quoteName($table) . $where . ';');
		$result = $this->shlLoadResult();

		return !empty($result);
	}

	/**
	 * Returns true if a record exists with a given Id
	 *
	 * @param String $table , the table to look into
	 * @param Integer $id , the id to look for
	 * @param String $idName , default to 'id', the columns to look into, if not 'id'
	 */
	public function shlIsRecordById($table, $id, $idName = 'id')
	{

		$id = (int) $id;

		if (empty($id))
		{
			return false;
		}

		// get db and look up record
		$this->shlSetSelectQuery($table, array($idName), array($id));
		$result = $this->shlLoadResult();

		return !empty($result);
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadResult()
	{

		$this->_isShLibHelper = true;
		$result = $this->loadResult();
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}
		$this->_isShLibHelper = false;
		return $result;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 * @deprecated Use shlLoadColumn() instead
	 */
	public function shlLoadResultArray($numinarray = 0)
	{

		ShlSystem_Log::debug('shlib', 'Using deprecated ShlDbClass::shlLoadResultArray() method. Use ShlDbClass::shlLoadColumn() instead');
		return $this->shlLoadColumn($numinarray);
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadColumn($offset = 0)
	{

		$this->_isShLibHelper = true;
		$array = $this->loadColumn($offset);
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;
		return $array;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadAssoc()
	{

		$this->_isShLibHelper = true;
		$result = $this->loadAssoc();
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;
		return $result;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadAssocList($key = '')
	{

		$this->_isShLibHelper = true;
		$result = $this->loadAssocList($key);
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;

		return $result;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadObject($className = 'stdClass')
	{

		$this->_isShLibHelper = true;
		$object = $this->loadObject($className);
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;
		return $object;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlLoadObjectList($key = '', $className = 'stdClass')
	{

		$this->_isShLibHelper = true;
		$objectList = $this->loadObjectList($key, $className);
		$error = $this->getErrorNum();
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;
		return $objectList;
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlQuery()
	{

		return $this->shlExecute();
	}

	/**
	 * Wrapper around J! method, which
	 * throws exceptions
	 */
	public function shlExecute()
	{

		$this->_isShLibHelper = true;
		$status = $this->execute();
		$error = empty($status) ? $this->getErrorNum() : '';
		if (!empty($error))
		{
			throw new ShlDbException($this->getErrorMsg(), $error);
		}

		$this->_isShLibHelper = false;
		return $this;
	}

	/**
	 * Base J! object overrides. These 3 methods are shortcuts
	 * implemented by Joomla! through a __call() magic method
	 * As we override Joomla! own __call() method
	 * we must re-implement those ourselves for B/C compat
	 *
	 */

	public function q()
	{
		$args = func_get_args();
		return $this->quote($args[0], isset($args[1]) ? $args[1] : true);
	}

	public function nq()
	{
		$args = func_get_args();
		return $this->quoteName($args[0]);
	}

	public function qn()
	{
		$args = func_get_args();
		return $this->quoteName($args[0]);
	}

	/**
	 * Base J! object overrides, to allow query caching
	 *
	 */

	/**
	 * Method to get the first row of the result set from the database query as an associative array
	 * of ['field_name' => 'row_value'].
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadAssoc()
	{

		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadAssoc');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadAssoc();

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadAssoc');

		return $read;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an associative array
	 * of ['field_name' => 'row_value'].  The array of rows can optionally be keyed by a field name, but defaults to
	 * a sequential numeric array.
	 *
	 * NOTE: Chosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string $key The name of a field on which to key the result array.
	 * @param   string $column An optional column name. Instead of the whole row, only this column value will be in
	 * the result array.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadAssocList($key = null, $column = null)
	{

		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadAssocList');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadAssocList($key, $column);

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadAssocList');

		return $read;
	}

	/**
	 * Method to get an array of values from the <var>$offset</var> field in each row of the result set from
	 * the database query.
	 *
	 * @param   integer $offset The row offset to use to build the result array.
	 *
	 * @return  mixed    The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadColumn($offset = 0)
	{
		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadColumn');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadColumn($offset);

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadColumn');

		return $read;
	}

	/**
	 * Method to get the first row of the result set from the database query as an object.
	 *
	 * @param   string $class The class name to use for the returned row object.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadObject($class = 'stdClass')
	{

		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadObject');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadObject($class);

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadObject');

		return $read;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an object.  The array
	 * of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field name can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string $key The name of a field on which to key the result array.
	 * @param   string $class The class name to use for the returned row objects.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadObjectList($key = '', $class = 'stdClass')
	{
		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadObjectList');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadObjectList($key, $class);

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadObjectList');

		return $read;
	}

	/**
	 * Method to get the first field of the first row of the result set from the database query.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadResult()
	{
		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadResult');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadResult();

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadResult');

		return $read;
	}

	/**
	 * Method to get the first row of the result set from the database query as an array.  Columns are indexed
	 * numerically so the first column in the result set would be accessible via <var>$row[0]</var>, etc.
	 *
	 * @return  mixed  The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadRow()
	{
		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadRow');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadRow();

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadRow');

		return $read;
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an array.  The array
	 * of objects can optionally be keyed by a field offset, but defaults to a sequential numeric array.
	 *
	 * NOTE: Choosing to key the result array by a non-unique field can result in unwanted
	 * behavior and should be avoided.
	 *
	 * @param   string $key The name of a field on which to key the result array.
	 *
	 * @return  mixed   The return value or null if the query failed.
	 *
	 * @since   11.1
	 * @throws  JDatabaseException
	 */
	public function loadRowList($key = null)
	{
		// check cache
		$cachedValue = $this->_shlQueryCacheRead('loadRowList');
		if ($cachedValue !== false)
		{
			return $cachedValue;
		}

		// call decorated object
		$read = $this->_decorated->loadRowList($key);

		// and store in cache
		$this->_shlQueryCacheStore($read, 'loadRowList');

		return $read;
	}

	/**
	 * Workarounds: we have to implement those 2 proxies to cover for
	 * Joomla!'s implementation that uses reference for one of the
	 * method params. This is not compatible with our decorator
	 * technique, which uses __call() and call_user_func()
	 * that cannot handle references.
	 */
	public function updateObject($table, &$object, $key, $nulls = false)
	{
		return $this->_decorated->updateObject($table, $object, $key, $nulls);
	}

	public function insertObject($table, &$object, $key = null)
	{
		return $this->_decorated->insertObject($table, $object, $key);
	}

	/**
	 * Prepare data to be inserted in an sql statement
	 *
	 * @param mixed $data
	 */
	protected function _shlPrepareData($data)
	{

		// from Ron Baldwin <ron.baldwin#sourceprose.com>
		// Only quote string types
		$type = gettype($data);
		if ($type == 'string')
		{
			$ret = $this->quote($data);
		}
		else if ($type == 'double')
		{
			$ret = str_replace(',', '.', $data); // locales fix so 1.1 does not get converted to 1,1
		}
		else if ($type == 'boolean')
		{
			$ret = $data ? '1' : '0';
		}
		else if ($type == 'object')
		{
			if (method_exists($data, '__toString'))
			{
				$ret = $this->quote($data->__toString());
			}
			else
			{
				$ret = $this->quote((string) $data);
			}
		}
		else if ($data === null)
		{
			$ret = 'NULL';
		}
		else
		{
			$ret = $data;
		}

		return $ret;
	}

	/**
	 * Build a where clause
	 *
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 */
	protected function _shlBuildWhereClause($mWhere = '', $aWhereData = array())
	{

		// where clause
		if (is_string($mWhere))
		{
			// litteral clause, find ? place holders
			if (!is_array($aWhereData))
			{
				$aWhereData = array($aWhereData);
			}
			$holderCount = substr_count($mWhere, '?');
			if ($holderCount > 0 && !empty($aWhereData) && $holderCount != count($aWhereData))
			{
				// the number of ? placehlders does not match the data array passed
				throw new ShlDbException(
					__METHOD__ . ': ' . 'Internal error: trying to build invalid db query where clause [ ' . serialize($mWhere) . ' ] [ '
					. serialize($aWhereData) . ' ]', 500
				);
			}
			else
			{
				// we have ? placeholders and their number equals that of data passed
				$where = '';

				// find placeholders
				if (empty($aWhereData))
				{
					$where = $mWhere;
				}
				else
				{
					$sqlBits = explode('?', $mWhere);
					$i = 0;
					// replace each place holder by the matching value
					foreach ($aWhereData as $data)
					{
						$where .= $sqlBits[$i];
						$where .= $this->_shlPrepareData($data);
						$i += 1;
					}
					if (isset($sqlBits[$i]))
					{
						$where .= $sqlBits[$i];
					}
				}
			}
		}
		elseif (is_array($mWhere))
		{
			// an array of columns/values, we must turn into a where clause
			$where = '';
			foreach ($mWhere as $columns => $value)
			{
				$where .= ' AND ' . $this->quoteName($columns) . '=' . $this->_shlPrepareData($value);
			}
			// remove initial AND
			$where = substr($where, 5);
		}
		else
		{
			$where = '';
		}

		return empty($where) ? '' : ' WHERE ' . $where;
	}

	/**
	 * Builds an ORDER BY sql statement
	 *
	 * $orderBy = 'title';
	 * $orderBy = array( 'extension' => '', 'title' => 'desc');
	 * $orderBy = array( 'extension', 'title');
	 *
	 * @param String $orderBy name of unique column to sort with, direction is always ASC
	 * @param Array $orderBy a list of column names to order by, direction is always asc
	 * @param Array $orderBy a list of key => values, where key is a column name, and value is either '', 'asc' or 'desc'
	 */
	protected function _shlBuildOrderByClause($orderBy)
	{

		if (empty($orderBy))
		{
			return '';
		}

		$clause = '';

		// 1: $orderBy is a string
		if (is_string($orderBy))
		{
			$clause = $this->quoteName($orderBy);
		}
		else if (is_array($orderBy))
		{
			foreach ($orderBy as $key => $value)
			{
				if (is_int($key))
				{
					// 2 : $orderBy is an array of strings
					// use directly, always with no direction
					$clause .= ', ' . $this->quoteName((string) $value);
				}
				else
				{
					// 3 : $orderBy is an array of column names with direction
					$clause .= ', ' . $this->quoteName($key) . (empty($value) ? '' : $this->escape($value));
				}
			}
			$clause = empty($clause) ? '' : substr($clause, 2);
		}

		// put everything together
		$clause = empty($clause) ? '' : ' ORDER BY ' . $clause;

		return $clause;
	}

	/**
	 * Builds a LIMIT sql statement
	 *
	 * @param Integer $offset , the line in result set to start with
	 * @param Integer $lines , the max number of lines in result set to return
	 */
	protected function _shlBuildLimitClause($offset, $lines)
	{

		if (empty($offset) && empty($lines))
		{
			return '';
		}

		$clause = ' LIMIT ';
		if (!empty($offset))
		{
			$clause .= $this->_shlPrepareData($offset);
		}
		if (!empty($lines))
		{
			$clause .= (empty($offset) ? '' : ', ') . $this->_shlPrepareData($lines);
		}

		return $clause;
	}

	/**
	 * Store the result of a db query into the query cache
	 * (if such feature is present and enabled)
	 *
	 * @param mixed $value the value to be stored
	 * @param string $prefix a prefix string to identify db operation, on top of sql query
	 * @return boolean $stored true if successfully stored, false if not
	 * @throws ShlDbException in case of cache internal error. Cache will be disabled if this happens (for current db instance)
	 *
	 */
	protected function _shlQueryCacheStore($value, $prefix)
	{

		$stored = false;

		if ($this->_shlCanCache())
		{
			try
			{
				//  cache id is made of db instance prepended with full sql query
				$stored = ShlCache_Manager::store($this->instanceName . $prefix . $this->getQuery(), self::CACHE_STATS_DATASET_NAME, $value);
			}
			catch (Exception $e)
			{
				// there was an error: log that and disable cache for this instance
				$this->queryCacheEnabled = false;
				ShlSystem_Log::error(
					'shlib',
					__METHOD__ . ': error storing data in query cache for instance %s, disabling cache for this instance (%s)', $this->instanceName,
					$e->getMessage()
				);
			}
		}

		return $stored;
	}

	/**
	 * Search the query cache for the previously stored
	 * result of a given database sql query
	 * (if such cache feature is present and enabled)
	 * The desired query must have been already prepared and set
	 * into the ShlDbClass database object passed to this method
	 *
	 * @param string $prefix a prefix string to identify db operation, on top of sql query
	 * @return mixed $read false if nothing found, whatever was found in the cache otherwise
	 * @throws ShlDbException in case of cache internal error. Cache will be disabled if this happens (for current db instance)
	 */
	protected function _shlQueryCacheRead($prefix)
	{

		$read = false;
		if ($this->_shlCanCache())
		{
			try
			{
				//  cache id is made of db instance prepended with full sql query
				$read = ShlCache_Manager::read($this->instanceName . $prefix . $this->getQuery(), self::CACHE_STATS_DATASET_NAME);
			}
			catch (Exception $e)
			{
				// there was an error: log that and disable cache for this instance
				$this->queryCacheEnabled = false;
				ShlSystem_Log::error(
					'shlib',
					__METHOD__ . ': error reading data in query cache for instance %s, disabling cache for this instance (%s)', $this->instanceName,
					$e->getMessage()
				);
			}
		}

		return is_null($read) ? false : $read;
	}

	/**
	 * Decide whether current opeation can read from/stored to
	 * query cache. Query cache must be enabled globally, but also,
	 * if this is a query coming directly from Joomla (ie not
	 * through our db helper class), query caching from Joomla should
	 * be enabled as well.
	 * Second condition is tha the query does not operate
	 * on one of the exluded db tables
	 */
	protected function _shlCanCache()
	{

		// 1st: enabled/disabled conditions
		if (!$this->queryCacheEnabled)
		{
			return false;
		}
		if (!$this->_isShLibHelper && !$this->queryCacheEnabledForJoomla)
		{
			return false;
		}
		$app = JFactory::getApplication();
		if (!$app->isSite())
		{
			return false;
		}

		// 2nd: does the query have excluded tables?
		$sql = $this->getQuery();
		foreach ($this->queryCacheTableExclusionList as $tableName)
		{
			if (strpos($sql, $tableName) !== false)
			{
				return false;
			}
		}

		// various conditions
		// if Joomla! content/categories queries with publish_up/down fields
		// don't cache, as these fields are changing every seconds
		if (strpos($sql, 'publish_up <=') !== false || strpos($sql, '>= a.publish_up') !== false || strpos($sql, 'publish_down >=') !== false
			|| strpos($sql, '<= a.publish_down') !== false
		)
		{
			return false;
		}

		return true;
	}
}
