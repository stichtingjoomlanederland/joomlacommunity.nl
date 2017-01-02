<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel;
use JDate;
use JLoader;

/**
 * Class SecurityExceptions
 *
 * @package Akeeba\AdminTools\Admin\Model
 *
 * @property   int     $id
 * @property   string  $logdate
 * @property   string  $ip
 * @property   string  $url
 * @property   string  $reason
 * @property   string  $extradata
 *
 * @property-read   int     $block
 *
 * @method  $this   datefrom() datefrom(string $v)
 * @method  $this   dateto() dateto(string $v)
 * @method  $this   groupbydate()  groupbydate(int $v)
 * @method  $this   groupbytype()  groupbytype(int $v)
 * @method  $this   reason()  reason(string $v)
 */
class SecurityExceptions extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		// We have a non-standard name (singular instead of plural)
		$config['tableName']   = '#__admintools_log';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);
	}

	public function buildQuery($overrideLimits = false)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
		            ->from($db->qn('#__admintools_log') . ' AS ' . $db->qn('l'));

		if ($this->getState('groupbydate', 0) == 1)
		{
			$this->addKnownField('date');
			$this->addKnownField('exceptions');

			$query->select(array(
				'DATE(' . $db->qn('l') . '.' . $db->qn('logdate') . ') AS ' . $db->qn('date'),
				'COUNT(' . $db->qn('l') . '.' . $db->qn('id') . ') AS ' . $db->qn('exceptions')
			));
		}
		elseif ($this->getState('groupbytype', 0) == 1)
		{
			$this->addKnownField('reason');
			$this->addKnownField('exceptions');

			$query->select(array(
				$db->qn('l') . '.' . $db->qn('reason'),
				'COUNT(' . $db->qn('l') . '.' . $db->qn('id') . ') AS ' . $db->qn('exceptions')
			));
		}
		else
		{
			$this->addKnownField('block');

			$query
				->select(array(
					$db->qn('l') . '.*',
					'CASE COALESCE(' . $db->qn('b') . '.' . $db->qn('ip') . ', ' . $db->q(0) . ') WHEN ' . $db->q(0)
					. ' THEN ' . $db->q('0') . ' ELSE ' . $db->q('1') . ' END AS ' . $db->qn('block')
				))
				->join('LEFT OUTER',
					$db->qn('#__admintools_ipblock') . ' AS ' . $db->qn('b') .
					'ON (' . $db->qn('b') . '.' . $db->qn('ip') . ' = ' .
					$db->qn('l') . '.' . $db->qn('ip') . ')'
				);
		}

		JLoader::import('joomla.utilities.date');

		$fltDateFrom = $this->getState('datefrom', null, 'string');

		if ($fltDateFrom)
		{
			$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

			if (!preg_match($regex, $fltDateFrom))
			{
				$fltDateFrom = '2000-01-01 00:00:00';
				$this->setState('datefrom', '');
			}

			$date = new JDate($fltDateFrom);
			$query->where($db->qn('logdate') . ' >= ' . $db->q($date->toSql()));
		}

		$fltDateTo = $this->getState('dateto', null, 'string');

		if ($fltDateTo)
		{
			$regex = '/^\d{1,4}(\/|-)\d{1,2}(\/|-)\d{2,4}[[:space:]]{0,}(\d{1,2}:\d{1,2}(:\d{1,2}){0,1}){0,1}$/';

			if (!preg_match($regex, $fltDateTo))
			{
				$fltDateTo = '2037-01-01 00:00:00';
				$this->setState('dateto', '');
			}

			$date = new JDate($fltDateTo);
			$query->where($db->qn('logdate') . ' <= ' . $db->q($date->toSql()));
		}

		$fltIP = $this->getState('ip', null, 'string');

		if ($fltIP)
		{
			$fltIP = '%' . $fltIP . '%';
			$query->where($db->qn('l') . '.' . $db->qn('ip') . ' LIKE ' . $db->q($fltIP));
		}

		$fltURL = $this->getState('url', null, 'string');

		if ($fltURL)
		{
			$fltURL = '%' . $fltURL . '%';
			$query->where($db->qn('url') . ' LIKE ' . $db->q($fltURL));
		}

		$fltReason = $this->getState('reason', null, 'cmd');

		if ($fltReason)
		{
			$query->where($db->qn('reason') . ' = ' . $db->q($fltReason));
		}

		$this->_buildQueryGroup($query);

		if ($this->getState('groupbydate', 0) == 1)
		{
			$query->order('DATE(' . $db->qn('l') . '.' . $db->qn('logdate') . ') ASC');
		}
		elseif ($this->getState('groupbytype', 0) == 1)
		{
			$query->order($db->qn('l') . '.' . $db->qn('reason') . ' ASC');
		}
		elseif (!$overrideLimits)
		{
			$order = $this->getState('filter_order', 'logdate', 'cmd');

			if (!in_array($order, array_keys($this->knownFields)))
			{
				$order = 'logdate';
			}

			$dir = $this->getState('filter_order_Dir', 'DESC', 'cmd');

			$query->order($order . ' ' . $dir);
		}

		return $query;
	}

	/**
	 * @param \JDatabaseQuery $query
	 */
	protected function _buildQueryGroup($query)
	{
		$db = $this->getDbo();

		if ($this->getState('groupbydate', 0) == 1)
		{
			$query->group(array(
				'DATE(' . $db->qn('l') . '.' . $db->qn('logdate') . ')'
			));
		}
		elseif ($this->getState('groupbytype', 0) == 1)
		{
			$query->group(array(
				$db->qn('l') . '.' . $db->qn('reason')
			));
		}
	}
}