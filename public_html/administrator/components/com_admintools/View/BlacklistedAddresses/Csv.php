<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\BlacklistedAddresses;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\View\DataView\Csv as BaseView;

class Csv extends BaseView
{
	/**
	 * List limit in the session, before coming to this view
	 *
	 * @var  int
	 */
	protected $saved_limit;

	/**
	 * List starting position in the session, before coming to this view
	 *
	 * @var  int
	 */
	protected $saved_limitstart;

	public function __construct(Container $container, array $config)
	{
		$config['csv_filename'] = 'ip_blacklist.csv';
		$config['csv_fields']   = array('ip', 'description');

		parent::__construct($container, $config);
	}

	protected function onBeforeBrowse()
	{
		$model = $this->getModel();

		// Let's save the current values for pagination
		$this->saved_limit      = $model->getState('limit', 0);
		$this->saved_limitstart = $model->getState('limitstart', 0);

		// Let's force the model to retrieve the whole list of IPs
		$model->setState('limit', 0);
		$model->setState('limitstart', 0);

		parent::onBeforeBrowse();
	}

	protected function onAfterBrowse()
	{
		$model = $this->getModel();

		// Let's revert the old values back
		$model->setState('limit', $this->saved_limit);
		$model->setState('limitstart', $this->saved_limitstart);
	}
}