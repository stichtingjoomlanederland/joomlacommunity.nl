<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.580
 * @date		2016-08-25
 */
/* Security check to ensure this file is being included by a parent file.*/
defined('_JEXEC') or die;

/**
 * Generic simple key/value storage
 *
 */
class ShlDb_Keystore
{
	/**
	 * Default db table name
	 *
	 *
	 * CREATE TABLE `XXXXX_wbl_keystore`
	 * (
	 * `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
	 * `key` VARCHAR(333) NOT NULL,
	 * `value` LONGTEXT NOT NULL,
	 * `user_id` INT NOT NULL,
	 * `modified_at`  DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
	 * `format` TINYINT DEFAULT 1 NOT NULL
	 * );
	 * CREATE INDEX `element_key` ON `XXXXX_wbl_keystore` (`key`);
	 *
	 */
	const TABLE_NAME = '#__wbl_keystore';

	/**
	 * Base format constant. Right now we de/serialize to and from php and json, and things are likely to stay like this
	 */
	const FORMAT_PHP  = 0;
	const FORMAT_JSON = 1;

	/**
	 * Do not encode
	 */
	const FORMAT_RAW = 128;

	/**
	 * Not supported yet
	 */
	const FORMAT_YAML = 2;

	/**
	 * default scope, when missing from requests
	 */
	const DEFAULT_SCOPE = 'default';

	/**
	 * Cache for current user id
	 *
	 * @var int|null
	 */
	protected $_userId = null;

	/**
	 * @var string name of db table to hold keystore values
	 */
	protected $_tableName = '';

	/**
	 * Singleton
	 *
	 * @var ShlDbKeystore
	 */
	private static $_instance = array();

	/**
	 * Public singleton
	 *
	 * @return ShlDbKeystore
	 */
	public static function getInstance($tableName = self::TABLE_NAME)
	{
		if (empty(self::$_instance[$tableName]))
		{
			self::$_instance[$tableName] = new self($tableName);
		}

		return self::$_instance[$tableName];
	}

	/**
	 * Store commonly used upstream object
	 * DB table to use for storage can be changed from the default wbl_keystore
	 *
	 * @param string $tableName
	 */
	private function __construct($tableName)
	{
		$this->_tableName = $tableName;
		$this->_userId = JFactory::getUser()->id;
	}

	/**
	 * Store data in keystore without any serialization
	 *
	 * @param string $key   unique id for the data
	 * @param mixed  $value data to store
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function putRaw($key, $value, $scope = self::DEFAULT_SCOPE)
	{
		if (!is_scalar($value) && !is_null($value))
		{
			throw new \InvalidArgumentException('Raw value passed to keystore is invalid, not scalar');
		}

		return $this->put($key, $value, $scope, self::FORMAT_RAW);
	}

	/**
	 * Store a value into the keystore, identified by a key. Overwrite any pre-existing value with same key.
	 * Value is serialized prior to being stored, using PHP serialization by default
	 * Alternative is json
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param int    $format use of the class constants
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function put($key, $value, $scope = self::DEFAULT_SCOPE, $format = self::FORMAT_PHP)
	{
		if (empty($key))
		{
			throw new \InvalidArgumentException('Empty key while trying to put some data in key store');
		}

		$data = array(
			'scope'       => $scope,
			'key'         => $key,
			'value'       => $this->_encode($value, $format),
			'user_id'     => $this->_userId,
			'modified_at' => ShlSystem_Date::getSiteNow(),
			'format'      => $format
		);

		// insert or update the record in database
		ShlDbHelper::insertUpdate($this->_tableName, $data, array('scope' => $scope, 'key' => $key));

		return $this;
	}

	/**
	 * Encode a value to one of the supported format, PHP serialization or json
	 *
	 * @param mixed $value
	 * @param int   $format see class constant
	 *
	 * @return string
	 */
	protected function _encode($value, $format)
	{
		switch ($format)
		{
			case self::FORMAT_PHP:
				$value = serialize($value);
				break;
			case self::FORMAT_JSON:
				$value = json_encode($value);
				break;
			default:
				break;
		}

		return $value;
	}

	/**
	 * Retrieves a value from the keystore, identified by its key.
	 * If not found, returns default value passed in.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed|null
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function get($key, $default = null, $scope = self::DEFAULT_SCOPE)
	{
		if (empty($key))
		{
			throw new \InvalidArgumentException('Empty key while trying to put some data in key store');
		}

		$record = ShlDbHelper::selectAssoc($this->_tableName, array('value', 'format'), array('scope' => $scope, 'key' => $key));
		$value = empty($record) ? null : $this->_decode($record['value'], $record['format']);
		$value = is_null($value) ? $default : $value;

		return $value;
	}

	/**
	 * Decode a raw value read from keystore, using the format also retrieved along the value.
	 * See class constants for format code.
	 *
	 * @param string $value
	 * @param int    $format
	 *
	 * @return mixed
	 */
	protected function _decode($value, $format)
	{
		switch ($format)
		{
			case self::FORMAT_PHP:
				$value = unserialize($value);
				break;
			case self::FORMAT_JSON:
				$value = json_decode($value);
				break;
			default:
				break;
		}

		return $value;
	}

	/**
	 * Delete a record in the keystore
	 *
	 * @param string $key
	 *
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	public function delete($key, $scope = self::DEFAULT_SCOPE)
	{
		if (empty($key))
		{
			throw new \InvalidArgumentException('Empty key while trying to delete some data from key store');
		}

		ShlDbHelper::delete($this->_tableName, array('scope' => $scope, 'key' => $key));

		return $this;
	}
}
