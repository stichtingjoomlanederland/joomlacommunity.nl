<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.587
 * @date        2016-10-31
 *
 * build 370
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Shlib system plugin
 *
 * @author
 */
class  plgSystemShlib extends JPlugin
{

	static public $__params = null;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		//load the translation strings
		JPlugin::loadLanguage('plg_system_shlib', JPATH_ADMINISTRATOR);

		self::$__params = $this->params;

		// prevent warning on php5.3+
		$this->_fixTimeWarning();

		// couple of base path
		defined('SHLIB_INSTALL_ROOT_PATH') or define('SHLIB_INSTALL_ROOT_PATH', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)));
		defined('SHLIB_ROOT_PATH') or define('SHLIB_ROOT_PATH', SHLIB_INSTALL_ROOT_PATH . '/shl_packages/');
		defined('SHLIB_LAYOUTS_PATH') or define('SHLIB_LAYOUTS_PATH', SHLIB_INSTALL_ROOT_PATH . '/layouts');
		defined('SHLIB_PATH_TO_ZEND') or define('SHLIB_PATH_TO_ZEND', SHLIB_ROOT_PATH . 'ZendFramework-1.11.7-minimal/library/');

		// register our autoloader
		require_once SHLIB_ROOT_PATH . 'system/autoloader.php';
		$initialized = ShlSystem_Autoloader::initialize(SHLIB_ROOT_PATH);

		// initialize path lib
		$this->_initLibrary();

		defined('SHLIB_VERSION') or define('SHLIB_VERSION', '0.3.1.587');
	}

	public function onAfterInitialise()
	{
		// check if we're set to enable database query cache
		if (!empty($this->params))
		{
			try
			{
				$handler = $this->params->get('sharedmemory_cache_handler', ShlCache_Manager::CACHE_HANDLER_APC);
				$queryCacheParams = array();
				$queryCacheParams['host'] = $this->params->get('sharedmemory_cache_host', '');
				$queryCacheParams['port'] = $this->params->get('sharedmemory_cache_port', '');
				ShlCache_Manager::setHandler($handler, $queryCacheParams);
				ShlDbHelper::switchQueryCache($this->params->get('enable_query_cache', 0) != 0);
				if ($this->params->get('enable_joomla_query_cache', 0))
				{
					ShlDbHelper::switchJoomlaQueryCache();
				}
			}
			catch (ShlException $e)
			{
				ShlSystem_Log::error('shlib', 'Unable to setup database query cache: %s', $e->getMessage());
			}
		}
	}

	public function onAfterRender()
	{
		if (ShlHtml_Manager::getInstance()->hasDeferredScripts())
		{
			$app = JFactory::getApplication();
			if (!$app->isSite())
			{
				return;
			}
			$document = JFactory::getDocument();

			if ($document->getType() != 'html')
			{
				return;
			}

			$scripts = ShlHtml_Manager::getInstance()->getDeferredScripts();
			$links = $scripts['links'];
			$declarations = $scripts['declarations'];

			// order scripts

			// build Text
			$linksHtml = '';
			foreach ($links as $linkRecord)
			{
				$linksHtml .= "\n<script src=\"" . $linkRecord['script'] . "\" type=\"text/javascript\"></script>\n";
			}
			$declarationsHtml = '';
			foreach ($declarations as $declarationRecord)
			{
				$declarationsHtml .= "\n<script type=\"text/javascript\">\n" . $declarationRecord['script'] . "\n</script>\n";
			}

			// insert in page
			$content = $linksHtml . $declarationsHtml;
			if (!empty($content))
			{
				$page = JResponse::getBody();
				$page = str_replace('</body>', $content . '</body>', $page);
				JResponse::setBody($page);
			}
		}
	}

	/**
	 * Respond to an ajax requests
	 *
	 * Requests are: /baseurl/index.php?option=com_ajax&plugin=shlib&method=xxxx&format=json
	 *
	 */
	public static function onAjaxShlib()
	{
		// token check from GET var
		if (!JSession::checkToken())
		{
			return new Exception(JText::_('JINVALID_TOKEN'));
		}

		$app = JFactory::getApplication();
		$app->allowCache(false);
		$method = $app->input->get('method');
		switch ($method)
		{
			case'removeMsg':
				ShlMsg_Manager::getInstance()->acknowledgeById($app->input->get('uid'));
				$response = array('success' => true, 'message' => 'Successfully called method ' . $method, 'messages' => null, 'data' => null);
				break;
			default:
				ShlSystem_Log::error('shLib', __METHOD__ . ': Invalid method called through ajax (' . $method . ').');
				$response = new Exception(JText::_('PLG_SYSTEM_WBREACTIV_INVALID_METHOD'));
				break;
		}

		return $response;
	}

	/**
	 *
	 * Prevent timezone not set warnings to appear all over,
	 * especially for PHP 5.3.3+
	 */
	protected function _fixTimeWarning()
	{
		@date_default_timezone_set(@date_default_timezone_get());
	}

	protected function _initLibrary()
	{
		// php shortcuts
		include_once SHLIB_ROOT_PATH . '/system/php_shortcuts.php';

		// initialize Zend autoloader
		include_once SHLIB_PATH_TO_ZEND . 'Zendshl/Loader/Autoloader.php';
		try
		{
			Zendshl_Loader_Autoloader::getInstance()->setZfPath(SHLIB_ROOT_PATH);
		}
		catch (Exception $e)
		{
		}

		// setup logging configuration according to params
		$logLevels = array();
		if (!empty($this->params))
		{
			if ($this->params->get('log_info'))
			{
				$logLevels[] = ShlSystem_Log::INFO;
			}
			if ($this->params->get('log_error'))
			{
				$logLevels[] = ShlSystem_Log::ERROR;
			}
			if ($this->params->get('log_alert'))
			{
				$logLevels[] = ShlSystem_Log::ALERT;
			}
			if ($this->params->get('log_debug'))
			{
				$logLevels[] = ShlSystem_Log::DEBUG;
			}
		}

		ShlSystem_Log::setConfig(array('logLevel' => $logLevels));
	}

}
