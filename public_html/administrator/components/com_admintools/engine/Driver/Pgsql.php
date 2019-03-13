<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Driver;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Driver\Query\Base as QueryBase;
use Akeeba\Engine\Driver\Query\Pgsql as QueryPgsql;

/**
 * PostgreSQL driver for Akeeba Engine
 *
 * Based on Joomla! Platform 12.1
 */
class Pgsql extends Postgresql
{
	/** @var \PDO The db connection resource */
	protected $connection = null;

	/** @var \PDOStatement The database connection cursor from the last query. */
	protected $cursor;

	/** @var array Driver options for PDO */
	protected $driverOptions = array();

	/**
	 * The database driver name
	 *
	 * @var string
	 */
	public $name = 'pgsql';

	/**
	 * Database object constructor
	 *
	 * @param   array $options List of options used to configure the connection
	 *
	 */
	public function __construct($options)
	{
		// Init
		$this->nameQuote = '"';

		// Finalize initialization
		parent::__construct($options);

		$this->driverType = 'pgsql';

		if (!is_object($this->connection))
		{
			$this->open();
		}
	}

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @throws  \RuntimeException
	 */
	public function open()
	{
		if ($this->connection)
		{
			return;
		}

		// Make sure the postgresql extension for PHP is installed and enabled.
		if (!self::isSupported())
		{
			throw new \RuntimeException('PDO extension is not available.');
		}

		$this->options['port'] = $this->options['port'] ? $this->options['port'] : 5432;
		$format                = 'pgsql:host=#HOST#;port=#PORT#;dbname=#DBNAME#';
		$replace               = ['#HOST#', '#PORT#', '#DBNAME#'];
		$with                  = [$this->options['host'], $this->options['port'], $this->options['database']];

		// Create the connection string:
		$connectionString = str_replace($replace, $with, $format);

		// connect to the server
		try
		{
			$this->connection = new \PDO(
				$connectionString,
				$this->options['user'],
				$this->options['password'],
				$this->driverOptions
			);
		}
		catch (\PDOException $e)
		{
			$this->errorNum = 2;
			$this->errorMsg = 'Could not connect to PDO: ' . $e->getMessage();

			return;
		}

		try
		{
			$this->connection->exec('SET standard_conforming_strings=off;');
			$this->connection->exec('SET escape_string_warning=off;');
		}
		catch (\Exception $e)
		{
		}

		$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

		if ($this->options['select'] && !empty($this->database))
		{
			$this->select($this->database);
		}

		$this->freeResult();
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 */
	public function close()
	{
		if (is_object($this->cursor))
		{
			$this->cursor->closeCursor();
		}

		$this->connection = null;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string  $text  The string to be escaped.
	 * @param   boolean $extra Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 */
	public function escape($text, $extra = false)
	{
		if (is_int($text) || is_float($text))
		{
			return $text;
		}

		$result = substr($this->connection->quote($text), 1, -1);

		if ($extra)
		{
			$result = addcslashes($result, '%_');
		}

		return $result;
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return    boolean
	 */
	public function connected()
	{
		if (!is_object($this->connection))
		{
			return false;
		}

		try
		{
			/** @var \PDOStatement $statement */
			$statement = $this->connection->prepare('SELECT 1');
			$executed  = $statement->execute();
			$ret       = 0;

			if ($executed)
			{
				$row = [0];

				if (!empty($statement) && $statement instanceof \PDOStatement)
				{
					$row = $statement->fetch(\PDO::FETCH_NUM);
				}

				$ret = $row[0];
			}

			$status = $ret == 1;

			$statement->closeCursor();
			$statement = null;
		}
			// If we catch an exception here, we must not be connected.
		catch (\Exception $e)
		{
			$status = false;
		}

		return $status;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return int The number of affected rows in the previous operation
	 */
	public function getAffectedRows()
	{
		if ($this->cursor instanceof \PDOStatement)
		{
			return $this->cursor->rowCount();
		}

		return 0;
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource $cur An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 */
	public function getNumRows($cur = null)
	{
		if ($cur instanceof \PDOStatement)
		{
			return $cur->rowCount();
		}

		if ($this->cursor instanceof \PDOStatement)
		{
			return $this->cursor->rowCount();
		}

		return 0;
	}

	/**
	 * Get the current or query, or new QueryBase object.
	 *
	 * @param   boolean  $new  False to return the last query set, True to return a new QueryBase object.
	 *
	 * @return  QueryBase  The current query object or a new object extending the QueryBase class.
	 *
	 * @throws  \RuntimeException
	 */
	public function getQuery($new = false)
	{
		if ($new)
		{
			return new QueryPgsql($this);
		}

		return $this->sql;
	}

	/**
	 * Get the version of the database connector.
	 *
	 * @return  string  The database connector version.
	 */
	public function getVersion()
	{
		return $this->connection->getAttribute(\PDO::ATTR_SERVER_VERSION);
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 */
	public function insertid()
	{
		// Error suppress this to prevent PDO warning us that the driver doesn't support this operation.
		return @$this->connection->lastInsertId();
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @throws  \RuntimeException
	 */
	public function query()
	{
		static $isReconnecting = false;

		if (!is_object($this->connection))
		{
			$this->open();
		}

		$this->freeResult();

		// Take a local copy so that we don't modify the original query and cause issues later
		$query = $this->replacePrefix((string) $this->sql);

		if ($this->limit > 0)
		{
			$query .= ' LIMIT ' . $this->limit;
		}

		if ($this->offset > 0)
		{
			$query .= ' OFFSET ' . $this->offset;
		}

		// Increment the query counter.
		$this->count++;

		// If debugging is enabled then let's log the query.
		if ($this->debug)
		{
			// Add the query to the object queue.
			$this->log[] = $query;
		}

		// Reset the error values.
		$this->errorNum = 0;
		$this->errorMsg = '';

		// Execute the query. Error suppression is used here to prevent warnings/notices that the connection has been lost.
		try
		{
			$this->cursor = $this->connection->query($query);
		}
		catch (\Exception $e)
		{
		}

		// If an error occurred handle it.
		if (!$this->cursor)
		{
			$errorInfo      = $this->connection->errorInfo();
			$this->errorNum = $errorInfo[1];
			$this->errorMsg = $errorInfo[2] . ' SQL=' . $query;

			// Check if the server was disconnected.
			if (!$this->connected() && !$isReconnecting)
			{
				$isReconnecting = true;

				try
				{
					// Attempt to reconnect.
					$this->connection = null;
					$this->open();
				}
					// If connect fails, ignore that exception and throw the normal exception.
				catch (\RuntimeException $e)
				{
					throw new \RuntimeException($this->errorMsg, $this->errorNum);
				}

				// Since we were able to reconnect, run the query again.
				$result         = $this->query();
				$isReconnecting = false;

				return $result;
			}
			// The server was not disconnected.
			else
			{
				throw new \RuntimeException($this->errorMsg, $this->errorNum);
			}
		}

		return $this->cursor;
	}

	/**
	 * Selects the database for use
	 *
	 * @param   string $database Database name to select.
	 *
	 * @return  boolean  Always true
	 */
	public function select($database)
	{
		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 */
	public function setUTF()
	{
		return true;
	}

	/**
	 * This function return a field value as a prepared string to be used in a SQL statement.
	 *
	 * @param   array  $columns     Array of table's column returned by ::getTableColumns.
	 * @param   string $field_name  The table field's name.
	 * @param   string $field_value The variable value to quote and return.
	 *
	 * @return  string  The quoted string.
	 */
	protected function sqlValue($columns, $field_name, $field_value)
	{
		switch ($columns[$field_name])
		{
			case 'boolean':
				$val = 'NULL';
				if (($field_value == 't') || ($field_value == '1'))
				{
					$val = 'TRUE';
				}
				elseif (($field_value == 'f') || ($field_value == ''))
				{
					$val = 'FALSE';
				}
				break;

			case 'bigint':
			case 'bigserial':
			case 'integer':
			case 'money':
			case 'numeric':
			case 'real':
			case 'smallint':
			case 'serial':
			case 'numeric,':
				$val = strlen($field_value) == 0 ? 'NULL' : $field_value;
				break;

			case 'date':
			case 'timestamp without time zone':
				if (empty($field_value))
				{
					$field_value = $this->getNullDate();
				}

				$val = $this->quote($field_value);

				break;

			default:
				$val = $this->quote($field_value);
				break;
		}

		return $val;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionCommit()
	{
		$this->connection->commit();
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @param   string $toSavepoint If present rollback transaction to this savepoint
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionRollback($toSavepoint = null)
	{
		$this->connection->rollBack();
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionStart()
	{
		$this->connection->beginTransaction();
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 */
	public function fetchArray($cursor = null)
	{
		$ret = null;

		if (!empty($cursor) && $cursor instanceof \PDOStatement)
		{
			$ret = $cursor->fetch(\PDO::FETCH_NUM);
		}
		elseif ($this->cursor instanceof \PDOStatement)
		{
			$ret = $this->cursor->fetch(\PDO::FETCH_NUM);
		}

		return $ret;
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 */
	public function fetchAssoc($cursor = null)
	{
		$ret = null;

		if (!empty($cursor) && $cursor instanceof \PDOStatement)
		{
			$ret = $cursor->fetch(\PDO::FETCH_ASSOC);
		}
		elseif ($this->cursor instanceof \PDOStatement)
		{
			$ret = $this->cursor->fetch(\PDO::FETCH_ASSOC);
		}

		return $ret;
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed  $cursor The optional result set cursor from which to fetch the row.
	 * @param   string $class  The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 */
	public function fetchObject($cursor = null, $class = 'stdClass')
	{
		$ret = null;

		if (!empty($cursor) && $cursor instanceof \PDOStatement)
		{
			$ret =  $cursor->fetchObject($class);
		}
		elseif ($this->cursor instanceof \PDOStatement)
		{
			$ret = $this->cursor->fetchObject($class);
		}

		return $ret;
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 */
	public function freeResult($cursor = null)
	{
		if ($cursor instanceof \PDOStatement)
		{
			$cursor->closeCursor();
			$cursor = null;
		}

		if ($this->cursor instanceof \PDOStatement)
		{
			$this->cursor->closeCursor();
			$this->cursor = null;
		}
	}

	/**
	 * Method to get the next row in the result set from the database query as an object.
	 *
	 * @param   string $class The class name to use for the returned row object.
	 *
	 * @return  mixed   The result of the query as an array, false if there are no more rows.
	 */
	public function loadNextObject($class = 'stdClass')
	{
		// Execute the query and get the result set cursor.
		if (!$this->cursor)
		{
			if (!($this->execute()))
			{
				return $this->errorNum ? null : false;
			}
		}

		// Get the next row from the result set as an object of type $class.
		if ($row = $this->fetchObject(null, $class))
		{
			return $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return false;
	}

	/**
	 * Method to get the next row in the result set from the database query as an array.
	 *
	 * @return  mixed  The result of the query as an array, false if there are no more rows.
	 */
	public function loadNextRow()
	{
		// Execute the query and get the result set cursor.
		if (!$this->cursor)
		{
			if (!($this->execute()))
			{
				return $this->errorNum ? null : false;
			}
		}

		// Get the next row from the result set as an object of type $class.
		if ($row = $this->fetchArray())
		{
			return $row;
		}

		// Free up system resources and return.
		$this->freeResult();

		return false;
	}

	/**
	 * Test to see if the PostgreSQL connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 */
	public static function isSupported()
	{
		return defined('PDO::ATTR_DRIVER_NAME');
	}

	/**
	 * PDO does not support serialize
	 *
	 * @return  array
	 *
	 * @throws  \ReflectionException
	 */
	public function __sleep()
	{
		$serializedProperties = array();

		$reflect = new \ReflectionClass($this);

		// Get properties of the current class
		$properties = $reflect->getProperties();

		foreach ($properties as $property)
		{
			// Do not serialize properties that are \PDO
			if ($property->isStatic() == false && !($this->{$property->name} instanceof \PDO))
			{
				array_push($serializedProperties, $property->name);
			}
		}

		return $serializedProperties;
	}

	/**
	 * Wake up after serialization
	 *
	 * @return  void
	 */
	public function __wakeup()
	{
		// Get connection back
		$this->__construct($this->options);
	}
}