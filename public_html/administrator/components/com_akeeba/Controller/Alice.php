<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
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
			'main', 'ajax', 'domains', 'translate'
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

	/**
	 * Translates language key in English strings
	 */
	public function translate()
	{
		$return  = array();
		$strings = $this->input->getString('keys', '');
		$strings = json_decode($strings);

		$lang = \JLanguage::getInstance('en-GB');
		$lang->load('com_akeeba');

		foreach ($strings as $string)
		{
			$temp['check'] = $lang->_($string->check);

			// If I have an array, it means that I have to use sprintf to translate the error
			if (is_array($string->error))
			{
				$trans[] = $lang->_($string->error[0]);
				$args    = array_merge($trans, array_slice($string->error, 1));
				$error   = call_user_func_array('sprintf', $args);
			}
			else
			{
				$error = $lang->_($string->error);
			}

			$temp['error'] = $error;

			$return[] = $temp;
		}

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($return) . '###';
		flush();

		$this->container->platform->closeApplication();
	}
}
