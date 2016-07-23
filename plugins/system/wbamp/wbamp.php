<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.4.2.551
 * @date        2016-07-19
 *
 * build 1.4.2.551
 *
 */

defined('_JEXEC') or die();

class plgSystemWbamp extends JPlugin
{
	/**
	 * List of document types we need to clean from
	 * wbamp custom tags when page is not an AMP page
	 *
	 * @var array
	 */
	private $documentTypesToScrub = array('html', 'feed');

	/**
	 * @var bool Whether current request is for an AMP page
	 */
	public $isAmpPage = false;

	/**
	 * @var bool Whether the current request has an AMP version
	 */
	public $hasAmpVersion = true;

	/**
	 * @var bool Internal on/off switch, saves time
	 */
	private $_enabled = true;

	/**
	 * @var WbampModel_Manager AMP manager model instance
	 */
	private $_manager = null;

	/**
	 * @var array Base URL to fetch updates
	 */
	private $baseUrls = array(
		'https://u1.weeblr.com/dist/wbamp/full' => 'wbamp',
		'https://u1.weeblr.com/dist/wbamp-themes-taylor/full' => 'wbamp-themes-taylor'
	);

	/**
	 * @var    String  extension identifier, to retrieve its params
	 */
	private $extension = 'plg_wbamp';

	/**
	 * @var    String    An id for your product, to be used by the web site when deciding to allow access
	 *                    Not mandatory, depends on subscription management system
	 */
	private $productId = 'wbamp';

	/**
	 * Cache whether site or admin
	 * @var bool
	 */
	private $_isSite = true;

	/**
	 * Stores a copy of the requested URI before
	 * the Joomla router can modify it
	 * (and remove trailing slash!)
	 *
	 * @var JUri|null
	 */
	private $_originalUri = null;

	/**
	 * Save the name of the currently
	 * selected template
	 *
	 * @var string
	 */
	private $_currentTemplate = '';

	/**
	 * Static class instance storage, for facade
	 *
	 * @var null|plgSystemWbamp
	 */
	static private $that = null;

	/**
	 * Flag to prevent multiple execution
	 *
	 * @var bool
	 */
	private $_alreadyRouted = false;

	/**
	 * Storage for joomla error handler
	 * to pass thru if we can't handle ourselves
	 *
	 * @var null
	 */
	private $_joomlaErrorHandler = null;

	/**
	 * Constructor detect if plugin should be started
	 * Enable and setup autoloader if so
	 *
	 * @param object $subject
	 * @param array $config
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// AMP pages only on front end
		$app = JFactory::getApplication();
		if (!$app->isSite())
		{
			$this->_enabled = false;
			$this->_isSite = false;
			$this->init($app);
			return;
		}

		// methods is GET
		$method = $app->input->getMethod();
		$allowedMethods = array('GET', 'HEAD');
		if (!in_array($method, $allowedMethods))
		{
			$this->_enabled = false;
			return;
		}

		// store for facade
		self::$that = $this;

		// run init procedure
		$inited = $this->init($app);
		if (!$inited)
		{
			return;
		}

		// register event handlers
		$joomlaRouter = $app->getRouter();
		$joomlaRouter->attachParseRule(array($this, 'preprocessParseRule'), JRouter::PROCESS_BEFORE);

		// store pristine URI, before J! router modifies it
		$this->_originalUri = JUri::getInstance();
	}

	/**
	 * Check for shLib presence and enable autoloader
	 *
	 * @param $app
	 * @return bool
	 */
	private function init($app)
	{
		// check shLib is available
		if (!defined('SHLIB_VERSION'))
		{
			$this->_enabled = false;
			if (!$app->isSite())
			{
				$app->enqueueMessage('Missing shLib, or not enabled, AMP plugin cannot run.', 'error');
			}
			return false;
		}

		// autoload our stuff
		$registered = ShlSystem_Autoloader::registerPrefix('Wbamp', JPATH_ROOT . '/plugins/system/wbamp', $isPackage = false);
		if (!$registered)
		{
			$this->_enabled = false;
			ShlSystem_Log::error('wbamp', 'Failed registering AMP plugin autoloader with shLib, AMP plugin cannot run.');
			return false;
		}

		// register error handling, in standalone mode
		if ($app->isSite() && WbampHelper_Edition::$id == 'full' && $this->params->get('operation_mode') == 'standalone')
		{
			// we store current handler, so as to pass-thru to it if needed
			$this->_joomlaErrorHandler = JError::getErrorHandling(E_ERROR);

			// then override Joomla! handler
			JError::setErrorHandling(E_ERROR, 'callback', array($this, 'errorHandler'));
			set_exception_handler(array($this, 'exceptionErrorHandler'));
		}

		return true;
	}

	/**
	 * Static facade for quick access to whether current request
	 * is for AMP content or not
	 *
	 * @return bool
	 */
	public static function isAmpPage()
	{
		return self::$that->isAmpPage;
	}

	/**
	 * Put our hands on the request before it's parsed, to identify
	 * the AMP suffix, and alter the request so that it's parsed
	 * normally. We raise a flag so that at a later stage we know
	 * this is an AMP request.
	 *
	 * @param $router
	 * @param $uri
	 */
	public function preProcessParseRule(&$router, &$uri)
	{
		if (!$this->_isSite || !$this->_enabled || $this->_alreadyRouted)
		{
			return;
		}

		// share some immutable data
		WbampHelper_Runtime::$base = $uri->base();
		WbampHelper_Runtime::$basePath = $uri->base(true);
		WbampHelper_Runtime::$params = $this->params;
		WbampHelper_Runtime::$joomlaConfig = JFactory::getConfig();

		// instantiate a new model, ask it to do the job
		$this->_manager = new WbampModel_Manager($router, $uri, $this->_originalUri);
		$this->isAmpPage = $this->_manager->parseAndUpdateRequest();

		// setup the API
		include_once('api.php');
		WbAMP::init($this->_manager);
	}

	/**
	 * After routing, we validate the AMP page detection
	 * for various situations
	 */
	public function onAfterRoute()
	{
		if (!$this->_isSite || $this->_alreadyRouted)
		{
			return;
		}
		$this->_alreadyRouted = true;

		if (!$this->_enabled)
		{
			return;
		}

		$docType = JFactory::getDocument()->getType();
		if ($docType != 'html')
		{
			$this->isAmpPage = false;
			$this->hasAmpVersion = false;
			return;
		}

		$app = JFactory::getApplication();
		$getInput = $app->input;
		$ruler = new WbampModel_Ruler($getInput, $this->_manager, $this->params, JFactory::getConfig());
		$passUserRules = $ruler->checkRules();
		if ($passUserRules)
		{
			$this->hasAmpVersion = true;

			// prepare rendering by Joomla, switch to our template
			// set template, to perform alternate template output, if set to
			if ($this->isAmpPage)
			{
				$this->_setAlternateTemplate();

				// disable caching: progressive caching may leave some bits
				// we actually don't want to see on the AMP pages
				// as some parts of the page are rendered without going
				// through our custom html and jlayouts overrides
				// because they are fetched directly from the cache
				WbampHelper_Runtime::$joomlaConfig->set('caching', 0);
			}
		}
		else
		{
			// if current request is for an AMP page,
			// set back to regular page, this will
			// trigger a 404 eventually.
			if ($this->isAmpPage && WbampHelper_Runtime::isStandaloneMode())
			{
				$this->loadLanguage();
				throw new JException(JText::_('PLG_SYSTEM_WBAMP_AMP_NOT_FOUND'), 404);
			}
			else if ($this->isAmpPage)
			{
				// load template language, it may not have been loaded yet
				$this->loadTemplateLanguage();
				$this->isAmpPage = false;
				throw new JException('', 404);
			}
			else
			{
				// regular html request, simply avoid advertising an AMP version
				$this->hasAmpVersion = false;
			}
		}
	}

	protected function loadTemplateLanguage()
	{
		$template = JFactory::getApplication()->getTemplate();

		// Load the language file for the template
		$lang = JFactory::getLanguage();

		// 1.5 or core then 1.6
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
		|| $lang->load('tpl_' . $template, 'templates' . '/' . $template, null, false, true);

		return $this;
	}

	/**
	 * Kill the Joomla email cloaker, which uses javascript
	 * We'll replace it with our own, encoding-based
	 *
	 * @param $context
	 * @param $row
	 * @param $params
	 * @param int $page
	 * @return bool
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		if (!$this->params->get('email_protection', true))
		{
			return true;
		}

		if (!$this->isAmpPage)
		{
			return true;
		}

		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return true;
		}

		if (is_object($row))
		{
			$row->text = '{emailcloak=off}' . $row->text;
		}
		else
		{
			$row = '{emailcloak=off}' . $row;
		}

		return true;
	}

	/**
	 * Add default JLayout folder to list. Can be extended by themes
	 * in the future
	 *
	 * @param $layoutPaths
	 */
	public function onWbAMPGetLayoutsPaths($theme, & $layoutPaths)
	{
		// always add the built-in, default layouts, as themes
		// may only partially override them
		array_unshift($layoutPaths, JPATH_ROOT . '/plugins/system/wbamp/layouts');
		return true;
	}

	/**
	 * Adds/modify CSS to the page
	 *
	 * @param $theme
	 * @param $css
	 * @param $displayData
	 * @return bool
	 */
	public function onWbAMPGetCss($theme, & $css, $displayData)
	{
		return true;
	}

	/**
	 * If current request has been found to be an AMP one
	 * we take over from there, at onAfterDispatch, as the
	 * main component has been rendered by Joomla and is available
	 * in the response object
	 */
	public function onAfterDispatch()
	{
		if (!$this->_isSite || !$this->_enabled)
		{
			return;
		}

		// check for HTTP status == 404 if sh404SEF
		// With Joomla SEF, onAfterDispatch is not triggered, so we don't
		// have to do this check
		if (defined('SH404SEF_IS_RUNNING')
			&& Sh404sefFactory::getPageInfo()->httpStatus == 404
		)
		{
			if (!WbampHelper_Runtime::isStandaloneMode())
			{
				// not handling 404s as an AMP page, returning control to Joomla
				return;
			}

			// standalone mode, jump to rendering wbAMP 404
			$error = new JException(JFactory::getDocument()->getBuffer('component'), 404);
			$this->errorHandler($error);
			return;
		}

		// not a 404, page must be rendered
		// - by us if we detected earlier this was an AMP page
		// - by Joomla otherwise, through we need to insert an rel=amphtml tag in such case
		if ($this->isAmpPage)
		{
			$displayData = $this->getData(JFactory::getApplication()->input);
			$this->renderAMPPage($displayData, 'wbamp.template');
		}
		else
		{
			$document = JFactory::getDocument();
			if ($this->hasAmpVersion && $this->params->get('operation_mode', 'normal') != 'development')
			{
				// not and AMP page, ie this is a regular page, for which
				// we must advertise the AMP page alternative page
				$ampLink = $this->_manager->getAMPUrl();
				$document->addHeadLink(htmlspecialchars($ampLink, ENT_COMPAT, 'UTF-8'), 'amphtml');
			}
		}
	}

	/**
	 * Gather all data required for a given AMP page output
	 *
	 * @param $getInput
	 * @return array
	 */
	private function getData($getInput)
	{
		// instantiate render model and view
		$renderer = new WbampModel_Renderer($getInput, $this->_manager, $this->isAmpPage);

		return $renderer->getData();
	}

	/**
	 * Renders and output a full AMP page,
	 * terminating the execution
	 *
	 * @param $getInput
	 */
	private function renderAMPPage($displayData, $mainLayout)
	{
		// initialize Layouts possible paths
		JPluginHelper::importPlugin('wbampthemes');
		ShlSystem_Factory::dispatcher()
		                 ->trigger(
			                 'onWbAMPGetLayoutsPaths',
			                 array($this->params->get('global_theme', 'weeblr.default'), & WbampHelper_Runtime::$layoutsBasePaths)
		                 );

		// let the view render the full page
		$view = new WbampView_Amp();
		$pageContent = $view->render($displayData, $mainLayout);

		// run hacks and custom compatibility code
		// required by 3rd-parties
		WbampHelper_Environment::handleSpecificEnvironment();

		// then terminate processing
		echo $pageContent;
		exit();
	}

	/**
	 * Clean up wbamp tags at the latest possible stage,
	 * to catch feeds
	 *
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();

		// If not an AMP page, clean it
		if ($this->_isSite && !$this->isAmpPage)
		{
			$document = JFactory::getDocument();
			if (in_array($document->getType(), $this->documentTypesToScrub))
			{
				// scrub custom wbamp tags from regular HTML content
				include_once 'helpers/content.php';
				$body = WbampHelper_Content::scrubRegularHtmlPage($app->getBody());
				$app->setBody($body);
			}
		}
	}

	/**
	 * Sets an alternate template, by default our own wbAMP template
	 * to reset all view output to a known markup
	 * (that can be later processed and fixed for AMP compliance as needed)
	 */
	protected function _setAlternateTemplate()
	{
		$templateId = (int) $this->params->get('rendering_template', 0);
		$templateName = WbampHelper_Media::getTemplateName($templateId);
		if (!empty($templateName))
		{
			// switch template
			$app = JFactory::getApplication();
			$this->_currentTemplate = $app->getTemplate();
			$app->setTemplate($templateName);
		}
	}

	/**
	 * Handle adding credentials to package download request
	 *
	 * @param   string $url url from which package is going to be downloaded
	 * @param   array $headers headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean    true        always true
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// are we trying to update one of our extensions?
		$oneOfOurs = false;
		foreach ($this->baseUrls as $baseUrl => $productId)
		{
			if (strpos($url, $baseUrl) === 0)
			{
				$oneOfOurs = true;
				$this->productId = $productId;
				break;
			}
		}

		if (!$oneOfOurs)
		{
			return true;
		}

		// needed to identify which edition is being used
		include_once JPATH_ROOT . '/plugins/system/wbamp/helpers/edition.php';

		// read credentials from extension params or any other source
		$credentials = $this->fetchCredentials($url, $headers);

		// bind credentials to request, either in the urls, or using headers
		// or a combination of both
		$this->bindCredentials($credentials, $url, $headers);

		return true;
	}

	/**
	 * Bind credentials to the download request.
	 *
	 * @param    array $credentials whatever credentials were retrieved for the current user/website
	 * @param   string $url url from which package is going to be downloaded
	 * @param   array $headers headers to be sent along the download request (key => value format)
	 *
	 * @return void
	 */
	private function bindCredentials($credentials, &$url, &$headers)
	{
		$headers['X-download-auth-ts'] = time();
		$headers['X-download-auth-id'] = $credentials['id'];
		$headers['X-download-auth-token'] = sha1($headers['X-download-auth-ts'] . mt_rand() . $credentials['secret'] . $url);
		$headers['X-download-auth-sig'] = sha1(
			$credentials['id'] . $headers['X-download-auth-token'] . $credentials['secret'] . $headers['X-download-auth-ts'] . $this->productId
			. WbampHelper_Edition::$id
		);
	}

	/**
	 * Retrieve user credentials
	 *
	 * @param $url
	 * @param $headers
	 *
	 * @return array credentials (id, secret)
	 */
	private function fetchCredentials($url, $headers)
	{
		// fetch credentials from extension parameters
		$credentials = array(
			'id' => trim($this->params->get('update_credentials_access', '')),
			'secret' => trim($this->params->get('update_credentials_secret', ''))
		);

		if (WbampHelper_Edition::$id == 'full'
			&& (empty($credentials['id']) || empty($credentials['secret']))
		)
		{
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('COM_WBAMP_UPDATE_NO_CREDENTIALS', 'https://weeblr.com/documentation/products.wbamp/1/installation-update/updating.html'), 'error');
			$app->redirect('index.php?option=com_installer&view=update');

		}
		return $credentials;
	}

	/**
	 * Handle all breaking errors output when
	 * in standalone mode, where we cannot rely
	 * on Joomla for output
	 *
	 * @param $error
	 */
	public function errorHandler(& $error)
	{
		$code = $error->getCode();

		// gather display data
		$displayData = $this->getData(JFactory::getApplication()->input);

		$this->loadLanguage();

		// render page
		JFactory::getConfig()->set('caching', 0);
		@ob_end_clean();
		ob_start();
		if (!headers_sent())
		{
			$displayData['headers'][] = 'Status: ' . $code;
		}

		// insert our error message
		$displayData['error_code'] = $code;
		$displayData['error_message'] = $error->getMessage();
		$codeBit = $code == 404 ? '_404' : '';
		$displayData['error_title'] = JText::sprintf('PLG_SYSTEM_WBAMP_ERROR' . $codeBit . '_TITLE', $displayData['error_code'], $displayData['error_message'], $displayData['amp_url']);
		$displayData['error_body'] = JText::sprintf('PLG_SYSTEM_WBAMP_ERROR' . $codeBit . '_BODY', $displayData['error_message'], $displayData['error_code'], $displayData['amp_url']);
		// sh404SEF integration
		if (
			defined('SH404SEF_IS_RUNNING')
			&& Sh404sefFactory::getPageInfo()->httpStatus == 404
			&& WbampHelper_Runtime::isStandaloneMode()
		)
		{
			// sh404SEF enabled, its similar URLs suggestions have been rendered
			// and passed in error message
			$displayData['error_title'] = '';
			$displayData['error_body'] = $displayData['error_message'];
		}

		$displayData['error_footer'] = JText::sprintf('PLG_SYSTEM_WBAMP_ERROR' . $codeBit . '_FOOTER', $displayData['error_code'], $displayData['error_message'], $displayData['amp_url']);

		$displayData['main_content'] = ShlMvcLayout_Helper::render('error_content', $displayData, WbampHelper_Runtime::$layoutsBasePaths);

		// finally render and exit
		$this->renderAMPPage($displayData, 'wbamp.error');
	}

	/**
	 * Exception proxy for our error handler
	 *
	 * @param $exception
	 */
	public function exceptionErrorHandler($exception)
	{
		$this->errorHandler($exception);
	}
}

