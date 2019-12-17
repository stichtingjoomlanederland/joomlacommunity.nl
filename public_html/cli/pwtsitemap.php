<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Application\CliApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Filesystem\File;

// Set flag that this is a parent file.
const _JEXEC = 1;

error_reporting(E_ALL | E_NOTICE);
ini_set('display_errors', 1);
ini_set('memory_limit','512M');

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
	require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__DIR__));
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_LIBRARIES . '/import.legacy.php';
require_once JPATH_LIBRARIES . '/cms.php';

// Define JDEBUG
define('JDEBUG', false);

// Fix Joomla! bug
$_SERVER['HTTP_HOST'] = null;

JLoader::register('PwtSitemapUrlHelper', JPATH_SITE . '/components/com_pwtsitemap/helpers/urlhelper.php');
JLoader::register('PwtSitemap', JPATH_SITE . '/components/com_pwtsitemap/models/sitemap/pwtsitemap.php');
JLoader::register('PwtSitemapItem', JPATH_SITE . '/components/com_pwtsitemap/models/sitemap/pwtsitemapitem.php');

JLoader::register('PwtSitemapPlugin', JPATH_SITE . '/components/com_pwtsitemap/models/plugin/pwtsitemapplugin.php');
JLoader::register('PlgPwtSitemapContact', JPATH_SITE . '/plugins/pwtsitemap/contact/contact.php');
JLoader::register('PlgPwtSitemapContent', JPATH_SITE . '/plugins/pwtsitemap/content/content.php');
JLoader::register('PlgPwtSitemapNewsfeed', JPATH_SITE . '/plugins/pwtsitemap/newsfeed/newsfeed.php');
JLoader::register('PlgPwtSitemapTag', JPATH_SITE . '/plugins/pwtsitemap/tag/tag.php');

/**
 * PwtSitemap CLI Script
 *
 * @since   1.4.0
 */
class PwtSitemapCli extends CliApplication
{
	/**
	 * The compression
	 *
	 * @var    boolean
	 * @since  1.4.0
	 */
	private $gz = false;

	/**
	 * The current domain, this is filled via the component params
	 *
	 * @var    string
	 * @since  1.4.0
	 */
	private $domain;

	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   1.4.0
	 *
	 * @throws  Exception
	 */
	public function doExecute()
	{
		$params = ComponentHelper::getParams('com_pwtsitemap');

		$this->domain = rtrim($params->get('domain'), '/');

		if (!$this->domain || stripos($this->domain, 'http') === false)
		{
			throw new Exception('Please configure a domain in the settings of this component');
		}

		/** @var AdministratorApplication $app */
		$app = Factory::getApplication('administrator');

		// We need to init the Joomla! session to register events.
		Factory::getApplication()->getSession()->set('user', User::getInstance(0));

		PluginHelper::importPlugin('system', 'languagefilter');
		PluginHelper::importPlugin('pwtsitemap');

		/**
		 * Requesting the dispatcher from app will throw an exception, so we use the deprecated method
		 * Calling this ensures the language filter plugin is properly initialised
		 */
		$app->triggerEvent('onAfterInitialise');

		// Re-init after the language plugin has done it's thing so we get frontend menu's instead of backend
		$app = Factory::getApplication('site');

		// Get current date
		$currentDate = Factory::getDate('now')->format('Y-m-d');

		// Register the events for plugins
		$app->registerEvent('onPwtSitemapBeforeBuild', 'PlgPwtSitemapContent');
		$app->registerEvent('onPwtSitemapBuildSitemap', 'PlgPwtSitemapContent');

		// Load the model.
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_pwtsitemap/models', 'PwtSitemapModel');

		/** @var PwtSitemapModelSitemap $sitemapModel */
		$sitemapModel = BaseDatabaseModel::getInstance('Sitemap', 'PwtSitemapModel');

		// Start the rebuild
		$this->out('Loading sitemaps items (this process could take some time) ...');

		array_map(
			static function ($filename) {
				if (stripos($filename, 'sitemap') !== false)
				{
					try
					{
						unlink(JPATH_ROOT . '/' . $filename);
					}
					catch (Exception $e)
					{
					}
				}
			},
			Folder::files(JPATH_ROOT, '.xml')
		);

		$sitemap = $sitemapModel->getSitemap();

		// Start main xml
		$mainXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$mainXml .= "<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

		$counter = 1;

		foreach ($sitemap->sitemapItems as $key => $menu)
		{
			foreach ($menu as $item)
			{
				$xmlString = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
				$xmlString .= "<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";

				foreach ($item as $i)
				{
					$xmlString .= $i->renderXml();
				}

				$xmlString .= "</urlset>\n";

				// Get the filename
				$gz       = $this->gz === true ? '.gz' : '';
				$filename = 'sitemap' . $counter . '.xml' . $gz;

				// Check for compression
				if ($this->gz === true)
				{
					$xmlString = gzcompress($xmlString, 9);
				}

				File::write(JPATH_ROOT . '/' . $filename, $xmlString);

				// Get the correct link for file
				$mainLink = $this->domain . '/' . $filename;

				$mainXml .= "\t<sitemap>\n";
				$mainXml .= "\t\t<url>\n";
				$mainXml .= "\t\t\t<loc>" . $mainLink . "</loc>\n";
				$mainXml .= "\t\t\t<lastmod>" . $currentDate . "</lastmod>\n";
				$mainXml .= "\t\t</url>\n";
				$mainXml .= "\t</sitemap>\n";

				++$counter;
			}
		}

		$mainXml .= "</sitemapindex>\n";

		File::write(JPATH_ROOT . '/sitemap.xml', $mainXml);
	}
}

CliApplication::getInstance('PwtSitemapCli')->execute();
