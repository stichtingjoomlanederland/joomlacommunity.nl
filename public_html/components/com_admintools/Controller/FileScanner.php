<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Site\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use Akeeba\AdminTools\Admin\Model\Scanner\Complexify;
use Akeeba\AdminTools\Admin\Model\Scanner\Util\Session;
use Akeeba\AdminTools\Site\Model\Scans;
use Exception;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use FOF30\Controller\Mixin\PredefinedTaskList;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Uri\Uri as JUri;

/**
 * Controller for the front-end PHP File Change Scanner feature
 */
class FileScanner extends Controller
{
	use PredefinedTaskList;

	/**
	 * FileScanner constructor.
	 *
	 * @param   Container  $container  The application container
	 * @param   array      $config     The configuration array
	 *
	 * @return  void
	 */
	public function __construct(Container $container, $config = [])
	{
		$config['csrfProtection'] = false;
		$this->predefinedTaskList = ['start', 'step'];
		$this->modelName          = 'Scans';

		parent::__construct($container, $config);
	}

	/**
	 * Starts a new front-end PHP File Change Scanner job
	 *
	 * @return  void
	 */
	public function start()
	{
		$this->enforceFrontendRequirements();

		/** @var Scans $model */
		$model = $this->getModel();

		$model->removeIncompleteScans();
		$this->resetPersistedEngineState();

		$resultArray = $model->startScan('frontend');

		$this->persistEngineState();
		$this->processResultArray($resultArray);
	}

	/**
	 * Steps through an already running front-end PHP File Change Scanner job
	 *
	 * @return  void
	 */
	public function step()
	{
		$this->enforceFrontendRequirements();
		$this->retrieveEngineState();

		/** @var Scans $model */
		$model       = $this->getModel();
		$resultArray = $model->stepScan();

		$this->persistEngineState();
		$this->processResultArray($resultArray);
	}

	/**
	 * Ensure that front-end scans are enabled and that the URL includes a correct, complex enough secret key.
	 *
	 * If any of these conditions is not met we return a 403.
	 *
	 * @return  void
	 */
	private function enforceFrontendRequirements()
	{
		// Is frontend backup enabled?
		$febEnabled = $this->container->params->get('frontend_enable', 0) != 0;

		// Is the Secret Key strong enough?
		$validKey = $this->container->params->get('frontend_secret_word', '');

		if (!Complexify::isStrongEnough($validKey, false))
		{
			$febEnabled = false;
		}

		if (!$febEnabled)
		{
			@ob_end_clean();
			echo '403 ' . JText::_('COM_ADMINTOOLS_ERROR_NOT_ENABLED');
			flush();

			$this->container->platform->closeApplication();

			return;
		}

		// Is the key good?
		$key          = $this->input->get('key', '', 'raw', 2);
		$validKeyTrim = trim($validKey);

		if (($key != $validKey) || (empty($validKeyTrim)))
		{
			@ob_end_clean();
			echo '403 ' . JText::_('COM_ADMINTOOLS_ERROR_INVALID_KEY');
			flush();

			$this->container->platform->closeApplication();
		}
	}

	/**
	 * Immediately issue a custom redirection and close the application.
	 *
	 * Unlike the regular Controller::redirect() this acts immediately and does not go through Joomla. Therefore we can
	 * use custom HTTP headers.
	 *
	 * @param   string  $url     URL to redirect to
	 * @param   string  $header  HTTP/1.1 header to use. Default: 302 Found (temporary redirection)
	 */
	private function issueRedirection($url, $header = '302 Found')
	{
		header('HTTP/1.1 ' . $header);
		header('Location: ' . $url);
		header('Content-Type: text/plain');
		header('Connection: close');

		$this->container->platform->closeApplication();
	}

	/**
	 * Process the scanner engine's result array and send the correct response to the browser
	 *
	 * This included issuing a custom redirection to the URL of the next step if such a thing is necessary. In either
	 * case, the application is immediately closed right at the end of this method's execution.
	 *
	 * @param   array  $resultArray  The result array to parse
	 *
	 * @return  void
	 */
	private function processResultArray(array $resultArray)
	{
		// Is this an error?
		if ($resultArray['error'] != '')
		{
			$this->resetPersistedEngineState();

			// An error occured
			die('500 ERROR -- ' . $resultArray['error']);
		}

		// Are we finished already?
		if ($resultArray['done'])
		{
			$this->resetPersistedEngineState();

			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo '200 OK';
			flush();

			$this->container->platform->closeApplication();

			return;
		}

		// We have more work to do. Should we redirect...?
		$noredirect = $this->input->get('noredirect', 0, 'int');

		if ($noredirect != 0)
		{
			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo "301 More work required";
			flush();

			$this->container->platform->closeApplication();

			return;
		}

		$curUri  = JUri::getInstance();
		$ssl     = $curUri->isSSL() ? 1 : 0;
		$tempURL = JRoute::_('index.php?option=com_admintools', false, $ssl);
		$uri     = new JUri($tempURL);

		$uri->setVar('view', 'FileScanner');
		$uri->setVar('task', 'step');
		$uri->setVar('key', $this->input->get('key', '', 'raw', 2));

		// Maybe we have a multilingual site?
		$languageTag = $this->container->platform->getLanguage()->getTag();

		$uri->setVar('lang', $languageTag);

		$this->issueRedirection($uri->toString());
	}

	/**
	 * Resets the persisted scanner engine state.
	 *
	 * @return  void
	 */
	private function resetPersistedEngineState()
	{
		$storage = new Storage();
		$storage->setValue('filescanner.memory', null);
		$storage->setValue('filescanner.timestamp', 0);
		$storage->save();
	}

	/**
	 * Persist the scanner engine state in the database.
	 *
	 * @return  void
	 */
	private function persistEngineState()
	{
		$session     = Session::getInstance();
		$storage     = new Storage();
		$sessionData = array_combine($session->getKnownKeys(), array_map(function ($key) use ($session) {
			return $session->get($key);
		}, $session->getKnownKeys()));

		$storage->setValue('filescanner.memory', json_encode($sessionData));
		$storage->setValue('filescanner.timestamp', time());
		$storage->save();
	}

	/**
	 * Retrieve the persisted scanner engine state from the database.
	 *
	 * It will result in a 403 error if there is no state, the state is invalid or it was stored more than 90 seconds
	 * ago.
	 *
	 * @return  void
	 */
	private function retrieveEngineState()
	{
		// Retrieve the engine's session from Admin Tools' storage in the database
		$storage    = new Storage();
		$jsonMemory = $storage->getValue('filescanner.memory', null);
		$timestamp  = $storage->getValue('filescanner.timestamp', 0);
		$valid      = !empty($jsonMemory) && (time() - $timestamp <= 90);
		try
		{
			$sessionData = @json_decode($jsonMemory, true);
		}
		catch (Exception $e)
		{
			$sessionData = null;
		}

		// If we have no data stored, invalid data stored or it was stored more than 90 seconds ago we can't proceed.
		if (!$valid || is_null($sessionData))
		{
			$this->resetPersistedEngineState();

			@ob_end_clean();
			echo '403 ' . JText::_('COM_ADMINTOOLS_ERROR_NOT_ENABLED');
			flush();

			$this->container->platform->closeApplication();
		}

		// Populate the session from the persisted state
		$session = Session::getInstance();
		array_walk($sessionData, function ($value, $key) use ($session) {
			$session->set($key, $value);
		});
	}
}
