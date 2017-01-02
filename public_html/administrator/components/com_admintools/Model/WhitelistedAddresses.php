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
 * @property   string  $ip
 * @property   string  $description
 *
 * @method  $this  ip()  ip(string $v)
 * @method  $this  description()  description(string|array $v)
 */
class WhitelistedAddresses extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		// We have a non-standard name
		$config['tableName']   = '#__admintools_adminiplist';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');
	}

	public function check()
	{
		if (!$this->ip)
		{
			throw new \Exception(\JText::_('COM_ADMINTOOLS_ERR_WHITELISTEDADDRESS_NEEDS_IP'));
		}

		return parent::check();
	}
}