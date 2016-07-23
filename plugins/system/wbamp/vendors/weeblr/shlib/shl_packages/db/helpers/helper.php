<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2015
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.540
 * @date				2016-07-18
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

class ShlDbHelper
{
	const STRING = 1;
	const INTEGER = 2;

	const SHL_DEFAULT = '__default';
	const DEFAULT_WEIGHT = 1;

	const OP_TYPE_DEFAULT = 'both';
	const OP_TYPE_WRITE = 'write';
	const OP_TYPE_READ = 'read';
	const OP_TYPE_BOTH = 'both';

	// performance flag
	private static $_multiDbSupportEnabled = false;

	// global query cache switch
	private static $_queryCacheEnabled = false;

	// global Joomla db query cache switch
	private static $_joomlaQueryCacheEnabled = false;

	// list of tables that should be excluded from query caching
	private static $_defaultQueryCacheTableExclusionList = array('#__sh404sef_urls', '#__extensions', '#__schemas', '#__session',
		'#__update_categories', '#__update_sites', '#__update_sites_extensions', '#__updates');

	// global query cache timeout, in seconds
	private static $_queryCacheDefaultTTL = 300;

	// holds currently selected database
	private static $_selectedDb = '';
	// holds currently selected dataset
	private static $_selectedDataset = '';

	// holds all registered databases records
	private static $_databases = array();

	// holds datasets information, ie which db to use for what usage
	private static $_datasets = array();

	public static $totalRequests = 0;
	public static $cacheHits = 0;
	public static $cacheStore = 0;

	/**
	 * Returns an instance of database connnection
	 * creating it if needed
	 * @TODO: should this really be public?
	 *
	 * @param string $name optional name of database instance to use
	 * @param string $opType optional type of operation we want an instance for (r/w, r/o, w/o)
	 * @return database object
	 */
	public static function &getInstance($name = '', $opType = self::OP_TYPE_BOTH)
	{
		// if user has not requested a specific db instance, decide which one to use
		if (empty($name))
		{
			$name = self::$_selectedDb;
		}

		// still no db? user had not previously set a specific db, using selectDb() method
		// next we search for the db associated with a default or user set dataset
		if (self::$_multiDbSupportEnabled && empty($name))
		{
			$dataset = empty(self::$_selectedDataset) ? self::SHL_DEFAULT : self::$_selectedDataset;
			$name = self::_findInstanceName($dataset, $opType);
		}

		// stil no name? we'll have to create a default new instance
		$name = empty($name) ? self::SHL_DEFAULT : $name;

		// if we don't have a db record by that name, create it with default values
		if (empty(self::$_databases[$name]))
		{
			self::_createDefaultDatabase($name);
		}

		// we should have a db instance by now
		$selectedInstance = self::$_databases[$name]->db;

		// return selected instance
		return $selectedInstance;
	}

	public static function &getDb()
	{
		return self::getInstance();
	}

	public static function getCacheStats()
	{
		$stats = array();

		if (!empty(self::$_databases))
		{
			foreach (self::$_databases as $name => $instance)
			{
				$stats[$name] = $instance->db->shlGetCacheStats();
			}
		}

		return $stats;
	}

	/**
	 * Globally enable or disable the query cache
	 * Applies only to READ db actions
	 *
	 * @param boolean $value either true or false, to enable or disable the cache resp.
	 * @return boolean previous value of query cache enabled flag
	 */
	public static function switchQueryCache($value = true)
	{
		$previous = self::$_queryCacheEnabled;
		self::$_queryCacheEnabled = $value;

		return $previous;
	}

	/**
	 * Globally enable or disable the query cache for the Joomla DB instance
	 * Applies only to READ db actions
	 *
	 * @param boolean $value either true or false, to enable or disable the cache resp.
	 * @param array $list array of table names, using Joomla notation: #__table_name
	 * @return boolean true if successful
	 */
	public static function switchJoomlaQueryCache($value = true, $exclusionList = null)
	{
		$switched = false;

		// see #287, Joomla! 3+ does not allow decorating the db object anymore
		// as has type hint for JDatabaseDriver in various places
		if (version_compare(JVERSION, '3.0.0', 'ge'))
		{
			return $switched;
		}
		// store value
		self::$_joomlaQueryCacheEnabled = $value;

		// compute exclusion list value
		$exclusionList = is_null($exclusionList) ? self::$_defaultQueryCacheTableExclusionList : $exclusionList;

		// contrary to if using this db helper, we must now actually
		// enable or disable the query cache on Joomla db instance
		// so that it becomes active also for Joomla and other extensions
		// that do not use this helper to perform db operations
		try
		{
			$joomlaDb = self::getInstance(self::SHL_DEFAULT);
			$joomlaDb->queryCacheEnabledForJoomla = $value;
			$joomlaDb->queryCacheTableExclusionList = $exclusionList;

			// now override JOomla instance
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				JFactory::$database = $joomlaDb; // 1.6+
			}
			else
			{
				$db = JFactory::getDbo();
				$db = $joomlaDb; // 1.5
			}
			$switched = true;
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('shlib', __METHOD__ . ': unable to switch query cache for joomla ' . ($value ? 'on' : 'off'));
		}
		return $switched;
	}

	/**
	 * Enable or disable the query cache on a particular db instance
	 * Applies only to READ db actions
	 *
	 * @param string $name db instance name, if empty will apply to default instance
	 * @param boolean $value either true or false, to enable or disable the cache resp.
	 * @return object $db the database instance modified
	 */
	public static function switchInstanceQueryCache($name = '', $value = true)
	{
		$db = self::getInstance($name, self::OP_TYPE_READ);
		$db->queryCacheEnabled = !empty($value);

		return $db;
	}

	/**
	 * Set default list of db tables that should disable
	 * query caching, when used in a query
	 *
	 * @param array $list array of table names, using Joomla notation: #__table_name
	 * @return array previous list
	 * @throws ShlException
	 */
	public static function setDefaultExcludedTablesList($list)
	{
		if (!is_array($list))
		{
			throw new ShlException(__METHOD__ . ': excluded tables list not an array');
		}

		$previous = self::$_defaultQueryCacheTableExclusionList;
		self::$_defaultQueryCacheTableExclusionList = $list;

		return $previous;
	}

	/**
	 * Get default list of db tables that should disable
	 * query caching, when used in a query
	 *
	 * @return array list, as an array of strings
	 */
	public static function getDefaultExcludedTablesList()
	{
		return self::$_defaultQueryCacheTableExclusionList;
	}

	/**
	 * Set default timeout for query cache servers
	 * Useful only for memcache and similar cache systems
	 *
	 * @param integer $newTTL
	 * @return integer previous value of TTL
	 * @throws ShlException
	 */
	public static function setDefaultTTL($newTTL)
	{
		$newTTL = (int) $newTTL;

		if ($newTTL < 0)
		{
			throw new ShlException(__METHOD__ . ': trying to set negative global TTL');
		}

		$previous = self::$_queryCacheDefaultTTL;
		self::$_queryCacheDefaultTTL = $newTTL;

		return $previous;
	}

	/**
	 *
	 * Set up and store a database instance to use
	 *
	 * @param string $name reference name for the instance
	 * @param object $db Joomla database object
	 * @param string $dataset name of dataset this db can be used for
	 * @param string $opType the type of db operation this db can be used for (read, write, both)
	 * @param boolean $enableQueryCache if true, query cache will be enabled on queries to this database
	 * @param array $list array of table names, using Joomla notation: #__table_name
	 * @return object the database full record
	 * @throws ShlDbException
	 */
	public static function registerDb($name, &$db, $dataset = self::SHL_DEFAULT, $opType = self::OP_TYPE_DEFAULT, $weight = self::DEFAULT_WEIGHT,
		$enableQueryCache = true, $exclusionList = null)
	{
		if (empty($name))
		{
			throw new ShlDbException(__METHOD__ . ': Trying to register a database object with no valid name');
		}

		if ((int) $weight < 1)
		{
			throw new ShlDbException(__METHOD__ . ': Trying to register a database object with an invalid weight: Must be greater than 1');
		}

		// compute exclusion list value
		$exclusionList = is_null($exclusionList) ? self::$_defaultQueryCacheTableExclusionList : $exclusionList;

		// store the actual db information
		self::$_databases[$name] = new ShlDbClass_Dbrecord($db, $name, $weight, $enableQueryCache, $exclusionList, self::$_joomlaQueryCacheEnabled);

		// then record what it's good for
		switch ($opType)
		{
			case self::OP_TYPE_READ:
			case self::OP_TYPE_WRITE:
				self::$_datasets[$dataset][$opType][] = $name;
				break;
			case self::OP_TYPE_BOTH:
				self::$_datasets[$dataset][self::OP_TYPE_READ][] = $name;
				self::$_datasets[$dataset][self::OP_TYPE_WRITE][] = $name;
				break;
			default:
				throw new ShlDbException(__METHOD__ . ': Trying to set database object with invalid operation type');
				break;
		}

		// clean up to avoid double recordings
		if (!empty(self::$_datasets[$dataset][self::OP_TYPE_READ]))
		{
			self::$_datasets[$dataset][self::OP_TYPE_READ] = array_values(array_unique(self::$_datasets[$dataset][self::OP_TYPE_READ]));
		}
		if (!empty(self::$_datasets[$dataset][self::OP_TYPE_WRITE]))
		{
			self::$_datasets[$dataset][self::OP_TYPE_WRITE] = array_values(array_unique(self::$_datasets[$dataset][self::OP_TYPE_WRITE]));
		}

		// store that we now have more than one db
		self::$_multiDbSupportEnabled = true;

		return self::$_databases[$name];
	}

	/**
	 *
	 * Disable one particular database instance
	 * registered with the class. The instance will not
	 * be used anymore for db requests, until it is
	 * re-enabled using self::enableDb()
	 *
	 * @param string $name reference name for the instance
	 * @return object the database full record
	 * @throws ShlDbException
	 */
	public static function disableDbInstance($name)
	{
		if ($name == self::SHL_DEFAULT)
		{
			throw new ShlDbException(__METHOD__ . ': Trying to disable the default database instance');
		}

		if (!empty(self::$_databases[$name]))
		{
			self::$_databases[$name]->enabled = false;
		}
		else
		{
			throw new ShlDbException(__METHOD__ . ': Trying to disable a non existing database instance');
		}

		// perf flag: if only one (or none) db instance left enabled, disable multi db support
		self::$_multiDbSupportEnabled = self::_hasMoreThanOneEnabledDb();

		return self::$_databases[$name];
	}

	/**
	 * Enable a particular database instance registered
	 * with the class, that may have been disabled previously
	 * using self::disableDb();
	 *
	 * @param string $name reference name for the instance
	 * @return object the database full record
	 * @throws ShlDbException
	 */
	public static function enableDbInstance($name)
	{
		if (!empty(self::$_databases[$name]))
		{
			self::$_databases[$name]->enabled = true;
		}
		else
		{
			throw new ShlDbException(__METHOD__ . ': Trying to enable a non existing database instance');
		}

		self::$_multiDbSupportEnabled = self::_hasMoreThanOneEnabledDb();

		return self::$_databases[$name];
	}

	/**
	 *
	 * Set the currently selected dataset, will be used
	 * for subsequent operations of this helper class, until
	 * another one is selected
	 *
	 * @param string $name name of dataset to select
	 * @return string previously selected dataset, if any
	 * @throws ShlDbException
	 */
	public static function selectDataset($name = '')
	{
		if (!empty($name) && empty(self::$_datasets[$name]))
		{
			throw new ShlDbException(__METHOD__ . ': Trying to select a dataset that has not been set: ' . $name);
		}

		$previous = self::$_selectedDataset;
		self::$_selectedDataset = $name;

		return $previous;
	}

	/**
	 * Reset selected dataset record to none
	 *
	 */
	public static function unselectDataset()
	{
		$previous = self::$_selectedDataset;
		self::$_selectedDataset = '';

		return $previous;
	}

	/**
	 *
	 * Set the currently selected database instance, will be used
	 * for subsequent operations of this helper class, until
	 * another one is selected
	 *
	 * @param string $name name of database instance to select
	 * @return string previously selected dataset, if any
	 * @throws ShlDbException
	 */
	public static function selectDb($name = '')
	{
		if (!empty($name) && empty(self::$_databases[$name]))
		{
			throw new ShlDbException(__METHOD__ . ': Trying to select a database that has not been set: ' . $name);
		}

		$previous = self::$_selectedDb;
		self::$_selectedDb = $name;

		return $previous;
	}

	/**
	 * Reset selected database record to none
	 *
	 */
	public static function unselectDb()
	{
		$previous = self::$_selectedDb;
		self::$_selectedDb = '';
		return $previous;
	}

	/**
	 * Prepare, set and execute a select query, returning a single result
	 *
	 * usage:
	 *
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', array( 'nonsef' => 'index.php?option=com_content&view=article&id=12'));
	 * will select the 'alias' column where nonsef column is index.php?option=com_content&view=article&id=12
	 * Alternate where condition syntax:
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', 'amount > 0 and amount < ?', array( '100'));
	 * If where condition is a string, it will be used literally, with question marks replaced by parameters as
	 * passed in the next method param. These params are escaped, but the base where condition is not
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectResult($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadResult();

		return $read;
	}

	/**
	 * Prepare, set and execute a select query, returning a an array of results
	 *
	 * usage:
	 *
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', array( 'nonsef' => 'index.php?option=com_content&view=article&id=12'));
	 * will select the 'alias' column where nonsef column is index.php?option=com_content&view=article&id=12
	 * Alternate where condition syntax:
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', 'amount > 0 and amount < ?', array( '100'));
	 * If where condition is a string, it will be used literally, with question marks replaced by parameters as
	 * passed in the next method param. These params are escaped, but the base where condition is not
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @deprecated Use selectColumn() instead
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectResultArray($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		ShlSystem_Log::debug('shlib', 'Using deprecated ShlDbHelper::selectResultArray() method. Use ShlDbHelper::selectColumn() instead');
		return self::selectColumn($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);
	}

	/**
	 * Prepare, set and execute a select query, returning a an array of results
	 *
	 * usage:
	 *
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', array( 'nonsef' => 'index.php?option=com_content&view=article&id=12'));
	 * will select the 'alias' column where nonsef column is index.php?option=com_content&view=article&id=12
	 * Alternate where condition syntax:
	 * $result = ShlHelperDb::selectResult( '#__sh404sef_alias', 'alias', 'amount > 0 and amount < ?', array( '100'));
	 * If where condition is a string, it will be used literally, with question marks replaced by parameters as
	 * passed in the next method param. These params are escaped, but the base where condition is not
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectColumn($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadColumn();

		return $read;
	}

	/**
	 * Prepare, set and execute a select query, returning a single associative array
	 *
	 * usage:
	 *
	 * $result = ShlHelperDb::selectAssoc( '#__sh404sef_alias', array('alias', 'id'), array( 'nonsef' => 'index.php?option=com_content&view=article&id=12'));
	 * will return an array with 2 keys, alias and id, where nonsef column is index.php?option=com_content&view=article&id=12
	 *
	 * $result = ShlHelperDb::selectAssoc( '#__sh404sef_alias', array('alias', 'id'), 'amount > 0 and amount < ?', array( '100'));
	 * If where condition is a string, it will be used literally, with question marks replaced by parameters as
	 * passed in the next method param. These params are escaped, but the base where condition is not
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectAssoc($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadAssoc();

		return $read;
	}

	/**
	 * Prepare, set and execute a select query, returning a an array of associative arrays
	 *
	 * usage:
	 *
	 * $result = ShlHelperDb::selectAssoc( '#__sh404sef_alias', array('alias', 'id'), array( 'nonsef' => 'index.php?option=com_content&view=article&id=12'));
	 * will return an array of arrays with 2 keys, alias and id, where nonsef column is index.php?option=com_content&view=article&id=12
	 *
	 * $result = ShlHelperDb::selectAssoc( '#__sh404sef_alias', array('alias', 'id'), 'amount > 0 and amount < ?', array( '100'));
	 * If where condition is a string, it will be used literally, with question marks replaced by parameters as
	 * passed in the next method param. These params are escaped, but the base where condition is not
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $key a column name to index the returned array with
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectAssocList($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $key = '', $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadAssocList($key);

		return $read;
	}

	/**
	 * Prepare, set and execute a select query, returning a single object
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectObject($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadObject();

		return $read;

	}

	/**
	 * Prepare, set and execute a select query, returning a an object list
	 *
	 * @param String $table The table name
	 * @param Array  $aColList array of strings of columns to be fetched
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param Array $orderBy, a list of columns to order the results
	 * @param Integer $offset, first line of result set to select
	 * @param Integer $lines, max number of lines to select
	 * @param string $key a column name to index the returned array with
	 * @param string $opType optional forced operation type for this operation
	 * @return mixed single value read from db
	 * @throw none (underlying database layer does throw errors)
	 */
	public static function selectObjectList($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $key = '', $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		// have db driver create the sql query
		$db = self::_setSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines, $opType);

		// if not in cache, run query
		$read = $db->shlLoadObjectList($key);

		return $read;
	}

	/**
	 * Prepare, set and execute a count query
	 *
	 * @param String $table The table name
	 * @param String $column optional column to be counted (defaults to *)
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function count($table, $column = '*', $mWhere = '', $aWhereData = array(), $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;

		$db = self::getInstance('', $opType);

		// have db driver create the sql query
		$db->shlSetCountQuery($table, $column, $mWhere, $aWhereData);

		// if not in cache, run query
		$read = $db->shlLoadResult();
		$read = empty($read) ? 0 : $read;

		return $read;
	}

	/**
	 * Prepare, set and execute a delete query
	 *
	 * @param String $table The table name
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function delete($table, $mWhere = '', $aWhereData = array(), $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		$db->shlSetDeleteQuery($table, $mWhere, $aWhereData)->shlQuery();

		return $db;
	}

	/**
	 * Prepare, set and execute a delete query based on a
	 * list of column value
	 *
	 * @param String $table The table name
	 * @param String $mwhereColumn name of column to compare to list of values
	 * @param Array $aWhereData List of column values that should be deleted
	 * @param Integer if self::INTEGER, list will be 'intvaled', else quoted
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function deleteIn($table, $mwhereColumn, $aWhereData, $type = self::STRING, $opType = '')
	{
		if (empty($mwhereColumn) || empty($aWhereData))
		{
			return;
		}

		// build a list of ids to read
		$wheres = $type == self::INTEGER ? self::arrayToIntvalList($aWhereData) : self::arrayToQuotedList($aWhereData);

		// perform deletion
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		return self::delete($table, $db->quoteName($mwhereColumn) . ' in (' . $wheres . ')');
	}

	/**
	 * Prepare, set and execute and insert query
	 *
	 * @param String $table The table name
	 * @param Array  $aData array of values pairs ( ie 'columnName' => 'columnValue')
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function insert($table, $aData, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		$db->shlSetInsertQuery($table, $aData)->shlQuery();

		return $db;

	}

	/**
	 * Prepare, set and execute an update query
	 *
	 * @param String $table The table name
	 * @param Array  $aData array of values pairs ( ie 'columnName' => 'columnValue')
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function update($table, $aData, $mWhere = '', $aWhereData = array(), $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		$db->shlSetUpdateQuery($table, $aData, $mWhere, $aWhereData)->shlQuery();

		return $db;

	}

	/**
	 * Prepare, set and execute an update query on a list
	 * of items
	 *
	 * @param String $table The table name
	 * @param Array  $aData array of values pairs ( ie 'columnName' => 'columnValue')
	 * @param String $mwhereColumn name of column to compare to list of values
	 * @param Array $aWhereData List of column values that should be updated
	 * @param Integer if self::INTEGER, list will be 'intvaled', else quoted
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function updateIn($table, $aData, $mwhereColumn, $aWhereData, $type = self::STRING, $opType = '')
	{
		if (empty($mwhereColumn) || empty($aWhereData))
		{
			return;
		}

		// build a list of ids to read
		$wheres = $type == self::INTEGER ? self::arrayToIntvalList($aWhereData) : self::arrayToQuotedList($aWhereData);

		// perform deletion
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		return self::update($table, $aData, $db->quoteName($mwhereColumn) . ' in (' . $wheres . ')');
	}

	/**
	 * Prepare, set and execute an insert or update query
	 *
	 * @param String $table The table name
	 * @param Array $aData An array of field to be inserted in the db ('columnName' => 'columnValue')
	 * @param String $mWhere Conditions. Taken as a litteral where clause ( WHERE `amount` > 100 ).
	 * @param Array $mWhere ( ie 'columnName' => 'columnValue') : a where clause is created like so : WHERE `columnName` = 'columnValue'. columnValue is escaped before being used
	 * @param Array $aWhereData Used only if $aWhere is a string. In such case, '?' place holders will be replaced by this array values, escaped
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function insertUpdate($table, $aData, $mWhere = '', $aWhereData = array(), $opType = '')
	{

		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		$db->shlSetInsertUpdateQuery($table, $aData, $mWhere, $aWhereData)->shlQuery();

		return $db;
	}

	/**
	 * Prepare, set and execute a custom database query
	 *
	 * @param String $query A litteral sql query
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function query($query, $opType = '')
	{
		$db = self::setQuery($query, $opType)->shlQuery();

		return $db;
	}

	/**
	 * Set a custom database query, so that
	 * another method can be chained to execute it
	 *
	 * @param String $query A litteral sql query
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function setQuery($query, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);
		$db->setQuery($query);

		return $db;
	}

	/**
	 *
	 * Prepare a query for running, quoting or name quoting some
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
	 * will result in running
	 *
	 *   select `id` from `#__table` where `counter` <> 'test'
	 *
	 *
	 * @param string $query
	 * @param array $nameQuoted
	 * @param array $quoted
	 * @param string $namePlaceHolder
	 * @param string $dataPlaceHolder
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function quoteQuery($query, $nameQuoted = array(), $quoted = array(), $namePlaceHolder = '??', $dataPlaceHolder = '?', $opType = '')
	{
		// get a db
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);

		// save query for error message
		$newQuery = $db->shlQuoteQuery($query, $nameQuoted, $quoted, $namePlaceHolder, $dataPlaceHolder);
		$db->setQuery($newQuery);

		return $db;
	}

	/**
	 *
	 * Runs a query, after quoting or name quoting some
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
	 * will result in running
	 *
	 *   select `id` from `#__table` where `counter` <> 'test'
	 *
	 *
	 * @param string $query
	 * @param array $nameQuoted
	 * @param array $quoted
	 * @param string $namePlaceHolder
	 * @param string $dataPlaceHolder
	 * @param string $opType optional forced operation type for this operation
	 * @return object the db object
	 */
	public static function runQuotedQuery($query, $nameQuoted = array(), $quoted = array(), $namePlaceHolder = '??', $dataPlaceHolder = '?',
		$opType = '')
	{
		// get a db
		$opType = empty($opType) ? self::OP_TYPE_WRITE : $opType;
		$db = self::getInstance('', $opType);

		// save query for error message
		$newQuery = $db->shlQuoteQuery($query, $nameQuoted, $quoted, $namePlaceHolder, $dataPlaceHolder);

		return self::query($newQuery);
	}

	/**
	 *
	 * Asks db to name quote a string
	 *
	 * @param string $string
	 */
	public static function nameQuote($string)
	{
		$db = self::getInstance();

		return $db->quoteName($string);
	}

	/**
	 *
	 * Asks DB to quote a string
	 * @param string $string
	 */
	public static function quote($string)
	{
		$db = self::getInstance();

		return $db->Quote($string);
	}

	/**
	 * Quote an array of value and turn it into a list
	 * of separated, name quoted elements
	 *
	 * @param array $data
	 * @param string $glue
	 * @return string
	 */
	public static function arrayToNameQuotedList($data, $glue = ',')
	{
		return self::_arrayToQuotedList($data, $nameQuote = true, $glue);
	}

	/**
	 * Quote an array of value and turn it into a list
	 * of separated, quoted elements
	 *
	 * @param array $data
	 * @param string $glue
	 * @return string
	 */
	public static function arrayToQuotedList($data, $glue = ',')
	{
		return self::_arrayToQuotedList($data, $nameQuote = false, $glue);
	}

	/**
	 * Quote an array of value and turn it into a list
	 * of separated, quoted elements
	 *
	 * @param array $data
	 * @param boolean $nameQuote if true, data is namedQuoted, otherwise Quoted
	 * @param string $glue
	 * @return string
	 */
	private static function _arrayToQuotedList($data, $nameQuote = false, $glue = ',')
	{
		$list = '';
		if (empty($data) || !is_array($data))
		{
			return $list;
		}

		$db = self::getInstance();
		$values = array();
		foreach ($data as $value)
		{
			$values[] = $nameQuote ? $db->quoteName($value) : $db->Quote($value);
		}

		$list = implode($glue, $values);

		return $list;
	}

	/**
	 * Intval an array of value and turn it into a list
	 * of separated, quoted elements
	 *
	 * @param array $data
	 * @param string $glue
	 * @return string
	 */
	public static function arrayToIntvalList($data, $glue = ',')
	{
		$list = '';
		if (empty($data) || !is_array($data))
		{
			return $list;
		}

		$values = array();
		foreach ($data as $value)
		{
			$values[] = (int) $value;
		}

		$list = implode($glue, $values);

		return $list;
	}

	protected static function _setSelectQuery($table, $aColList = array('*'), $mWhere = '', $aWhereData = array(), $orderBy = array(), $offset = 0,
		$lines = 0, $opType = '')
	{
		$opType = empty($opType) ? self::OP_TYPE_READ : $opType;
		$db = self::getInstance('', self::OP_TYPE_READ);
		$db->shlSetSelectQuery($table, $aColList, $mWhere, $aWhereData, $orderBy, $offset, $lines);

		return $db;
	}

	/**
	 * Select the instance to be used for the operation requested
	 * First based on operation type (read, write)
	 * Second, using a weighted random selection
	 *
	 * @param string $dataset dataset to use
	 * @param string $opType requested operation type
	 */
	private static function _findInstanceName($dataset, $opType)
	{
		$name = '';

		if (!empty(self::$_datasets[$dataset]))
		{
			// if we have a dataset (possibly the default one), we're going through
			// the selection process for a given instance

			// first, select by operation type: we grab the names of all db instances
			// that have been registered for this dataset, for each type of operation
			$instances = array();
			switch ($opType)
			{
				case self::OP_TYPE_READ:
				case self::OP_TYPE_WRITE:
					$instances = array_merge($instances, self::$_datasets[$dataset][$opType]);
					break;
				case self::OP_TYPE_BOTH:
					$instances = array_merge($instances, self::$_datasets[$dataset][self::OP_TYPE_READ]);
					$instances = array_merge($instances, self::$_datasets[$dataset][self::OP_TYPE_WRITE]);
					break;
			}
			$instances = array_values(array_unique($instances));
			// second, if there are several instances for the same operation type
			// select amongst them, using weight coefficients
			// note: $instances contains a list of db instances names
			$name = self::_weightedRandomSelect($instances);
		}

		// return whatever we found, can be empty
		return $name;
	}

	/**
	 * Select at random an instance, amongst the passed
	 * list of acceptable instances. Choice is not fully
	 * random, but use the weight coefficient of each instance
	 * (set by user when an instance is registered)
	 *
	 * @param array $instances names of the acceptable db instances
	 */
	private static function _weightedRandomSelect($instances)
	{
		$name = '';

		if (!empty($instances))
		{
			$weightedSums = array();
			$total = 0;
			foreach ($instances as $index => $instanceName)
			{
				if (self::$_databases[$instanceName]->enabled)
				{
					$total += self::$_databases[$instanceName]->weight;
					$weightedSums[$index] = $total;
				}
			}

			// if no instance is available, because
			// either no instance has been registered
			// or if some have been, they don't match the request
			// for instance we have only read instances
			// while we do a write query,
			// then nothing to select
			if (empty($weightedSums))
			{
				return '';
			}

			// we have at least one valid db
			// can proceed
			$pick = mt_rand(1, $total);
			foreach ($weightedSums as $index => $sum)
			{
				if ($sum >= $pick)
				{
					$name = $instances[$index];
					break;
				}
			}
		}

		return $name;
	}

	/**
	 * Create a default database record, using Joomla!
	 * database instance
	 *
	 * @param string $name name under which the db should be recorded
	 */
	private static function _createDefaultDatabase($name)
	{
		// get application db instance
		// that's hardcoded dependency, but allow transparent operation
		// within Joomla! framework, which will be 99%
		// of cases anyway
		$db = JFactory::getDBO();

		// prepare a record to hold our database instance details
		self::$_databases[$name] = new ShlDbClass_Dbrecord($db, $name, self::DEFAULT_WEIGHT, self::$_queryCacheEnabled,
			self::$_defaultQueryCacheTableExclusionList, self::$_joomlaQueryCacheEnabled);

		// store as well in default dataset
		self::$_datasets[self::SHL_DEFAULT][self::OP_TYPE_READ][] = $name;
		self::$_datasets[self::SHL_DEFAULT][self::OP_TYPE_WRITE][] = $name;

		// enable the multi db support flag if we
		// now have more than one database instance
		self::$_multiDbSupportEnabled = self::_hasMoreThanOneEnabledDb();

	}

	/**
	 * Finds if there is more than the default database instance
	 * enabled. If so raise a flag. This will be used by the
	 * class to avoid some unneeded processing in the
	 * (common) case we're only working with one single, default, instance
	 *
	 */
	private static function _hasMoreThanOneEnabledDb()
	{
		$enabled = false;
		foreach (self::$_databases as $name => $instance)
		{
			if ($name != self::SHL_DEFAULT && $instance->enabled)
			{
				$enabled = true;
				break;
			}
		}
		return $enabled;
	}
}
