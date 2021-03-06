<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') || die;

use FOF40\Container\Container;
use FOF40\Model\DataModel;

/**
 * @property   int    $id
 * @property   string $ip
 * @property   string $reason
 * @property   string $until
 *
 * @method  $this  ip()  ip(string $v)
 * @method  $this  reason()  reason(string $v)
 * @method  $this  until()  until(string $v)
 */
class IPAutoBanHistories extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_ipautobanhistory';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');
	}
}
