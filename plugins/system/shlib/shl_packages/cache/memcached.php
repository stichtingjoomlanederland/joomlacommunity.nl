<?php
/**
 * Shlib - programming library
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier 2016
 * @package     shlib
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     0.3.1.580
 * @date				2016-08-25
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Implements read and store methods
 * to decorate an ShlCache_Manager
 *
 * @author yannick
 *
 */
class ShlCache_Memcached extends ShlSystem_Abstractdecorator
{

	const DEFAULT_HOST = '127.0.0.1';
	const DEFAULT_PORT = 11211;
	const DEFAULT_PERSISTENT = true;
	const DEFAULT_WEIGHT = 1;
	const DEFAULT_TIMEOUT = 1;
	const DEFAULT_RETRY_INTERVAL = 15;
	const DEFAULT_COMPRESSION = 0;

	protected $_instanceParams = array();
	protected $_server = null;

	/**
	 * Check if setup is correct, ie APC extension is loaded in php
	 *
	 * @throws ShlException
	 */
	public function init($params)
	{
		$this->_instanceParams = $params;

		// maybe we're trying to call this a second time?
		if (class_exists('Memcache') && $this->_server instanceof Memcache)
		{
			return true;
		}

		// first call, check setup and initialize server connection data
		$this->enabled = extension_loaded('memcached');
		if (!$this->enabled)
		{
			ShlSystem_Log::error('shlib', __METHOD__ . ': Memcached extension not loaded, unable to create cache manager using it');
		}

		// sort of validate params, using defaults if missing
		if ($this->enabled)
		{
			$this->_instanceParams['host'] = empty($this->_instanceParams['host']) ? self::DEFAULT_HOST : $this->_instanceParams['host'];
			$this->_instanceParams['port'] = empty($this->_instanceParams['port']) ? self::DEFAULT_PORT : $this->_instanceParams['port'];
			$this->_instanceParams['weight'] = empty($this->_instanceParams['weight']) ? self::DEFAULT_WEIGHT : $this->_instanceParams['weight'];
			$this->_instanceParams['persistent'] = empty($this->_instanceParams['persistent']) ? self::DEFAULT_PERSISTENT: $this->_instanceParams['persistent'];

			// now establish connection
			$this->_connect($this->_instanceParams);
			$this->enabled = Memcached::RES_SUCCESS == @$this->_server->getResultCode();
			if (!$this->enabled)
			{
				ShlSystem_Log::error('shlib', __METHOD__ . ': Unable to add Memcache server %s:%s', $this->_instanceParams['host'],
					$this->_instanceParams['port']);
			}
		}
	}

	/**
	 * Connect to server, adding a new instance only if none is found
	 * @param unknown $params
	 * @return boolean
	 */
	protected function _connect($params)
	{
		// create a connection
		$this->_server = $params['persistent'] ? new Memcached('__') : new Memcached();
		$servers = $this->_server->getServerList();
		if (is_array($servers))
		{
			foreach ($servers as $server)
			{
				if ($server['host'] == $params['host'] and $server['port'] == $params['port'])
				{
					// already connected
					return true;
				}
			}
		}
		return @$this->_server->addServer($params['host'], $params['port'], $params['weight']);
	}

	public function doRead($id)
	{

		if ($this->enabled)
		{
			$read = @$this->_server->get($id);
			return $read;
		}
		else
		{
			throw new ShlException(__METHOD__ . ': trying to read from a disabled cache');
		}
	}

	public function doStore($id, $value, $ttl)
	{

		if ($this->enabled)
		{
			$stored = @$this->_server->set($id, $value, $ttl);
			if ($stored === false)
			{
				ShlSystem_Log::error('shlib', __METHOD__ . ': error storing memcached value %s - %s', @$this->_server->getResultCode(),
					@$this->_server->getResultMessage());
			}
			return $stored;
		}
		else
		{
			throw new ShlException(__METHOD__ . ': trying to write to a disabled cache');
		}
	}

	public function doRemove($id)
	{

		if ($this->enabled)
		{
			$removed = $this->_server->delete($id);
			return $removed;
		}
		else
		{
			throw new ShlException(__METHOD__ . ': trying to delete from a disabled cache');
		}
	}

	public function doClear()
	{

		if ($this->enabled)
		{
			$cleared = $this->_server->flush();
			return $cleared;
		}
		else
		{
			throw new ShlException(__METHOD__ . ': trying to clear a disabled cache');
		}
	}

}
