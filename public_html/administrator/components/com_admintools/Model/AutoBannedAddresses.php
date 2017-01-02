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

/**
 * @property   string  $ip      Blocked IP
 * @property   string  $reason  Block reason
 * @property   string  $until   Block until this date and time
 *                              
 * @method  $this  ip() ip(string $v)
 * @method  $this  reason() reason(string|array $v)
 * @method  $this  until() until(string|array $v)
*/
class AutoBannedAddresses extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_ipautoban';
		$config['idFieldName'] = 'ip';

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');
	}

	public function buildQuery($overrideLimits = false)
	{
		$db    = $this->getDbo();
		$query = parent::buildQuery($overrideLimits);

		$fltIP = $this->getState('ip', null, 'string');

		if ($fltIP)
		{
			$fltIP = '%' . $fltIP . '%';
			$query->where($db->qn('ip') . ' LIKE ' . $db->q($fltIP));
		}

		return $query;
	}
}