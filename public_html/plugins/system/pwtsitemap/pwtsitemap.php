<?php
/**
 * @package    Pwtsitemap
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

/**
 * PWT Sitemap System plugin
 *
 * @since  1.0.0
 */
class PlgSystemPwtSitemap extends CMSPlugin
{
	/**
	 * Automatic load plugin language files
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Joomla! Application instance
	 *
	 * @var    JApplicationSite
	 * @since  1.0.0
	 */
	public $app;

	/**
	 * Joomla! Database instance
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0.0
	 */
	public $db;

	/**
	 * @var    string  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://extensions.perfectwebteam.com/pwt-sitemap';

	/**
	 * @var    string  Extension identifier, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extension = 'com_pwtsitemap';

	/**
	 * @var    string  Extension title, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extensionTitle = 'PWT Sitemap';

	/**
	 * Load PwtSitemap plugin group and register helpers and classes
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAfterInitialise()
	{
		// Register base plugin class
		JLoader::register('PwtSitemapPlugin', JPATH_ROOT . '/components/com_pwtsitemap/models/plugin/pwtsitemapplugin.php');
		PluginHelper::importPlugin('pwtsitemap');

		JLoader::register('PwtSitemapUrlHelper', JPATH_ROOT . '/components/com_pwtsitemap/helpers/urlhelper.php');
		JLoader::register('PwtSitemapHelper', JPATH_ROOT . '/administrator/components/com_pwtsitemap/helpers/pwtsitemap.php');
		JLoader::register('PwtSitemap', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap/pwtsitemap.php');
		JLoader::register('PwtSitemapItem', JPATH_ROOT . '/components/com_pwtsitemap/models/sitemap/pwtsitemapitem.php');
	}

	/**
	 * Due to us having the format in the non-sef url which is sef'd, it might actually get lost when another plugin
	 * calls Factory::getDocument and the input isn't properly populated yet.
	 *
	 * @since  1.4.0
	 *
	 * @throws Exception If no application can be found
	 */
	public function onAfterRoute()
	{
		// We only have to check this if front and we looking for xml variant, as default html is fine
		if ($this->app->isClient('site')
			&& $this->app->input->get('format', 'html') === 'xml'
			&& Factory::getDocument()->getType() !== 'xml'
		)
		{
			// We have to reset the document otherwise it will just return html with xml body
			Factory::$document = null;
			Factory::getDocument();
		}
	}

	/**
	 * Add sitemap parameter to the menu edit form
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Make sure form element is a JForm object
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Make sure we are on the edit menu item page
		if ($form->getName() !== 'com_menus.item')
		{
			return true;
		}

		// Check authorization
		if (!Factory::getUser()->authorise('core.manage', 'com_pwtsitemap'))
		{
			return true;
		}

		// Load form.xml
		Form::addFormPath(__DIR__ . '/forms');
		$form->loadFile('pwtsitemap');

		return true;
	}

	/**
	 * Handle ajax request to change the status async
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onAjaxPwtSitemap()
	{
		$itemId    = $this->app->input->getInt('itemId');
		$parameter = $this->app->input->get('parameter');
		$value     = $this->app->input->getString('value');

		PwtSitemapHelper::saveMenuItemParameter($itemId, $parameter, $value);
	}

	/**
	 * Perform the onPwtSitemapBeforeBuild event. We strip all the menu-items which have a no-index value
	 *
	 * @param   array   $aMenuItems  The array holding the menu items
	 * @param   string  $sType       The sitemap type
	 * @param   string  $sFormat     The sitemap format
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	public function onPwtSitemapBeforeBuild(&$aMenuItems, $sType, $sFormat)
	{
		// Only for XML format
		if ($sFormat === 'xml')
		{
			// Check menu-items
			foreach ($aMenuItems as $iPK => $oItem)
			{
				// Remove no-index menu-items from sitemap
				if (strpos($oItem->params->get('robots'), 'noindex') !== false)
				{
					unset($aMenuItems[$iPK]);
				}

				// Remove alias menu-items from sitemap
				if ($oItem->type === 'alias')
				{
					unset($aMenuItems[$iPK]);
				}
			}
		}
	}

	/**
	 * Adding required headers for successful extension update
	 *
	 * @param   string  $url      url from which package is going to be downloaded
	 * @param   array   $headers  headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true    Always true, regardless of success
	 *
	 * @throws  Exception
	 * @since   1.0.0
	 *
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Are we trying to update our own extensions?
		if (strpos($url, $this->baseUrl) !== 0)
		{
			return true;
		}

		// Load language file
		$jLanguage = Factory::getLanguage();
		$jLanguage->load('com_pwtsitemap', JPATH_ADMINISTRATOR . '/components/com_pwtsitemap/', 'en-GB', true, true);
		$jLanguage->load('com_pwtsitemap', JPATH_ADMINISTRATOR . '/components/com_pwtsitemap/', null, true, false);

		// Append key to url if not set yet
		if (strpos($url, 'key') === false)
		{
			// Get the Download ID from component params
			$downloadId = ComponentHelper::getComponent($this->extension)->params->get('downloadid', '');

			// Check if Download ID is set
			if (empty($downloadId))
			{
				Factory::getApplication()->enqueueMessage(
					Text::sprintf('COM_PWTSITEMAP_DOWNLOAD_ID_REQUIRED',
						$this->extension,
						$this->extensionTitle
					),
					'error'
				);

				return true;
			}

			// Append the Download ID from component options
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url       .= $separator . 'key=' . trim($downloadId);
		}

		// Append domain to url if not set yet
		if (strpos($url, 'domain') === false)
		{
			// Get the domain for this site
			$domain = preg_replace('/(^https?:\/\/)/', '', rtrim(Uri::root(), '/'));

			// Append domain
			$url .= '&domain=' . $domain;
		}

		return true;
	}
}
