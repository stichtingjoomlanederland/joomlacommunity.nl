<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Controller\Mixin\CustomACL;
use Akeeba\Backup\Admin\Controller\Mixin\PredefinedTaskList;
use Akeeba\Engine\Platform;
use AliceUtilScripting;
use FOF30\Container\Container;
use FOF30\Controller\Controller;

/**
 * ALICE log analyzer controller
 */
class Alice extends Controller
{
	use CustomACL, PredefinedTaskList;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->setPredefinedTaskList([
			'main', 'ajax', 'domains'
		]);
	}

	/**
	 * Execute a step through AJAX
	 *
	 * @return  void
	 */
	public function ajax()
	{
		/** @var \Akeeba\Backup\Admin\Model\Alice $model */
		$model = $this->getModel();

		$model->setState('ajax', $this->input->get('ajax', '', 'cmd'));
		$model->setState('log', $this->input->get('log', '', 'cmd'));

		$ret_array = $model->runAnalysis();

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($ret_array) . '###';
		flush();

		$this->container->platform->closeApplication();
	}

	/**
	 * Get a list of all the log analysis domain names
	 *
	 * @return  void
	 */
	public function domains()
	{
		$return  = array();
		$domains = AliceUtilScripting::getDomainChain();

		foreach ($domains as $domain)
		{
			$return[] = array($domain['domain'], $domain['name']);
		}

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($return) . '###';
		flush();

		$this->container->platform->closeApplication();
	}
}