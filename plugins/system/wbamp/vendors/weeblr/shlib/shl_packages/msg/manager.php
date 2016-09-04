<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.580
 * @date         2016-08-25
 */

// Security check to ensure this file is being included by a parent file.
defined('_JEXEC') or die;

/**
 * Manages a message/notification center
 *
 *
 */
class ShlMsg_Manager
{
	const DISPLAY_TYPE_INFO          = 0;
	const DISPLAY_TYPE_NOTICE        = 1;
	const DISPLAY_TYPE_WARNING       = 2;
	const DISPLAY_TYPE_ERROR         = 3;
	const DISPLAY_TYPE_IMPORTANT     = 4;
	const ACTION_CAN_CLOSE           = 0;
	const ACTION_CANNOT_CLOSE        = 1;
	const ACTION_ON_CLOSE_DELAY_5MN  = 2;
	const ACTION_ON_CLOSE_DELAY_10MN = 3;
	const ACTION_ON_CLOSE_DELAY_15MN = 4;
	const ACTION_ON_CLOSE_DELAY_30MN = 5;
	const ACTION_ON_CLOSE_DELAY_1H   = 6;
	const ACTION_ON_CLOSE_DELAY_24H  = 7;
	const ACTION_ON_CLOSE_DELAY_7D   = 8;
	const ACTION_ON_CLOSE_DELAY_1M   = 9;
	static $displayTypeClasses = array();
	static $_manager           = null;

	private $_tableName = '#__wblib_messages';

	public static function getInstance()
	{
		if (is_null(self::$_manager))
		{
			self::$displayTypeClasses = array(
				self::DISPLAY_TYPE_INFO      => 'info',
				self::DISPLAY_TYPE_ERROR     => 'error',
				self::DISPLAY_TYPE_WARNING   => 'warning',
				self::DISPLAY_TYPE_NOTICE    => 'notice',
				self::DISPLAY_TYPE_IMPORTANT => 'important'
			);
			self::$_manager = new ShlMsg_Manager;
		}

		return self::$_manager;
	}

	/**
	 * Write a new message to the database
	 *
	 * @param array $msg Holds the message data
	 */
	public function add($msg)
	{
		// compute uid
		$msg['uid'] = sha1(serialize($msg) . mt_rand());

		// update creation _date
		$msg['created_on'] = ShlSystem_Date::getUTCNow();

		// store to db
		ShlDbHelper::insert($this->_tableName, $msg);
	}

	/**
	 * Adds a message only once and only once
	 *
	 * @param $msg
	 */
	public function addOnce($msg)
	{
		if (!$this->validate($msg))
		{
			return;
		};
		$found = ShlDbHelper::count($this->_tableName, 'id', array('scope' => $msg['scope'], 'type' => $msg['type'], 'sub_type' => $msg['sub_type']));
		if (empty($found))
		{
			$this->add($msg);
		}
	}

	/**
	 * Add a msg, only if it doesn't exists
	 * "Exists" is defined by an array of options
	 *
	 * @param array $msg
	 * @param array $options
	 */
	public function addIfNotExists($msg, $options)
	{
		if (!$this->validate($msg))
		{
			return;
		};
		$found = ShlDbHelper::count($this->_tableName, 'id', $options);
		if (empty($found))
		{
			$this->add($msg);
		}
	}

	/**
	 * Adds a message unless there's already one
	 * of the same type, not yet acknowledge by user
	 *
	 * @param $msg
	 */
	public function addUnlessNotAcknowledged($msg)
	{
		if (!$this->validate($msg))
		{
			return;
		};
		$found = ShlDbHelper::count($this->_tableName, 'id', array(
				'scope' => $msg['scope'], 'type' => $msg['type'], 'sub_type' => $msg['sub_type'], 'acked_on' => '0000-00-00 00:00:00'
			)
		);
		if (empty($found))
		{
			$this->add($msg);
		}
	}

	/**
	 * Count how many messages would be displayed according to the
	 * provided selection options
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function getCount($options = array())
	{
		return $this->get($options, true);
	}

	/**
	 * Gets messages from the db
	 *
	 * options: scope, type, display_type, uid, acknowledge
	 *
	 * @param array $options
	 * @return mixed
	 */
	public function get($options = array(), $countOnly = false)
	{
		try
		{
			if (empty($options['scope']))
			{
				throw new Exception('shLib: Empty scope trying to read messages');
			}

			$where = array();
			$db = ShlDbHelper::getDb();
			foreach ($options as $key => $value)
			{
				switch ($key)
				{
					case 'scope':
					case 'type':
					case 'sub_type':
					case 'display_type':
					case 'uid':
						if (!empty($options[$key]))
						{
							$where[] = $db->qn($key) . ' = ' . $db->q($value);
						}
						break;
					case 'acknowledged':
						if ($value)
						{
							$where[] = $db->qn('acked_on') . ' <> ' . $db->q('0000-00-00 00:00:00');
						}
						else
						{
							$where[] = $db->qn('acked_on') . ' = ' . $db->q('0000-00-00 00:00:00');
						}
						break;
				}
			}

			// hide or show after at give date
			$now = $db->q(ShlSystem_Date::getUTCNow());
			$where[] = '(' . $db->qn('hide_after') . ' = ' . $db->q('0000-00-00 00:00:00')
				. ' or '
				. $db->qn('hide_after') . ' > ' . $now
				. ')';
			$where[] = '(' . $db->qn('hide_until') . ' = ' . $db->q('0000-00-00 00:00:00')
				. ' or '
				. $db->qn('hide_until') . ' < ' . $now
				. ')';

			$whereClause = implode(' and ', $where);
			$orderBy = array('display_type' => 'DESC', 'created_on' => 'DESC');
			$msgs = $countOnly ? ShlDbHelper::count($this->_tableName, '*', $whereClause)
				: ShlDbHelper::selectObjectList($this->_tableName, '*', $whereClause, array(), $orderBy);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			$msgs = array();
		}

		return $msgs;
	}

	public function delete($options = array())
	{
		if (empty($options['scope']) || empty($options['type']))
		{
			throw new RuntimeException('ShLib: trying to delete msg without scope or type.', 404);
		}

		ShlDbHelper::delete($this->_tableName, $options);
	}

	public function acknowledgeById($uid)
	{
		if (empty($uid))
		{
			throw new RuntimeException('ShLib: Empty message id while trying to acknowledge message.', 404);
		}

		$this->acknowledge(array('uid' => $uid));
	}

	public function acknowledge($options, $force = false)
	{
		if (empty($options['acked_on']))
		{
			$options['acked_on'] = '0000-00-00 00:00:00';
		}

		// find record
		if ($force)
		{
			$action = self::ACTION_CAN_CLOSE;
		}
		else
		{
			$msg = ShlDbHelper::selectAssoc($this->_tableName, '*', $options);
			if (empty($msg))
			{
				throw new RuntimeException('ShLib: Cannot find db record trying to acknowledge a message.', 404);
			}
			$action = $msg['action'];
		}

		switch ($action)
		{
			case self::ACTION_CAN_CLOSE:
				ShlDbHelper::update($this->_tableName, array('acked_on' => ShlSystem_Date::getUTCNow()), $options);
				break;
			case self::ACTION_CANNOT_CLOSE:
				break;
			case self::ACTION_ON_CLOSE_DELAY_5MN:
			case self::ACTION_ON_CLOSE_DELAY_10MN:
			case self::ACTION_ON_CLOSE_DELAY_15MN:
			case self::ACTION_ON_CLOSE_DELAY_30MN:
			case self::ACTION_ON_CLOSE_DELAY_1H:
			case self::ACTION_ON_CLOSE_DELAY_24H:
			case self::ACTION_ON_CLOSE_DELAY_7D:
			case self::ACTION_ON_CLOSE_DELAY_1M:
				$hideUntil = new DateTime('now', new DateTimeZone('UTC'));
				$hideUntil->add(new DateInterval($this->getDelayFromActionCode($msg['action'])));
				ShlDbHelper::update($this->_tableName, array('hide_until' => $hideUntil->format('Y-m-d H:i:s')), $options);
				break;
			default:
				throw new RuntimeException('ShLib:Invalid action code trying to acknowledge a message' . $msg['action'] . '.', 404);
				break;
		}
	}

	public function addAssets($document, $options = array())
	{
		$htmlManager = ShlHtml_Manager::getInstance();
		$document->addStyleSheet($htmlManager->getMediaLink('msg', 'css', array('url_base' => JURI::root(true))));
		$document->addScript($htmlManager->getMediaLink('msg', 'js', array('url_base' => JURI::root(true))));
	}

	private function getDelayFromActionCode($actionCode)
	{
		switch ($actionCode)
		{
			case self::ACTION_ON_CLOSE_DELAY_5MN:
				$delay = 'PT5M';
				break;
			case self::ACTION_ON_CLOSE_DELAY_10MN:
				$delay = 'PT10M';
				break;
			case self::ACTION_ON_CLOSE_DELAY_15MN:
				$delay = 'PT15M';
				break;
			case self::ACTION_ON_CLOSE_DELAY_30MN:
				$delay = 'PT20M';
				break;
			case self::ACTION_ON_CLOSE_DELAY_1H:
				$delay = 'PT1H';
				break;
			case self::ACTION_ON_CLOSE_DELAY_24H:
				$delay = 'P1D';
				break;
			case self::ACTION_ON_CLOSE_DELAY_7D:
				$delay = 'P7D';
				break;
			case self::ACTION_ON_CLOSE_DELAY_1M:
				$delay = 'P1M';
				break;
			default:
				$delay = 0;
				break;
		}

		return $delay;
	}

	private function validate($msg)
	{
		if (empty($msg['scope']) || empty($msg['type']) || empty($msg['title']))
		{
			ShlSystem_Log::error('shlib', '%s::%d: %s', __METHOD__, __LINE__, 'Invalid message sent for storage ' . print_r($msg, true));
			return false;
		}

		return true;
	}
}
