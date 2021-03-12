<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * Main component view. This will display a dashboard and serves as a main entry point
 *
 * @since  1.0
 */
class PWTSEOViewPWTSEO extends HtmlView
{
	/**
	 * If we have also PWT Sitemap installed, we add a check if there is a menu-item
	 *
	 * @var     boolean
	 *
	 * @since   1.0.2
	 */
	protected $bHasSitemap;

	/**
	 * If we have a clientid, we check with google if there are sitemaps associated with this website
	 *
	 * @var     array
	 *
	 * @since   1.0.2
	 */
	protected $aSitemaps;

	/**
	 * Holds general SEO information from Google/Bing
	 *
	 * @var     array
	 *
	 * @since   1.3.1
	 */
	protected $aDomain;

	/**
	 * Display the view
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		PWTSEOHelper::addSubmenu('pwtseo');
		$this->sidebar = JHtmlSidebar::render();

		$this->addToolbar();

		$params = ComponentHelper::getParams('com_pwtseo');

		if ($params->get('clientid') && $params->get('clientsecret'))
		{
			try
			{
				$oAuth = new \JGoogleAuthOauth2;
				$oURI  = Uri::getInstance();

				$oAuth->setOption('clientid', trim($params->get('clientid')));
				$oAuth->setOption('clientsecret', trim($params->get('clientsecret')));
				$oAuth->setOption('sendheaders', true);
				$oAuth->setOption('scope', 'https://www.googleapis.com/auth/webmasters.readonly');

				// The redirecturi is specified because of possible mismatches
				$oAuth->setOption('redirecturi', $oURI->getScheme() . '://' . $oURI->getHost() . '/administrator/index.php?option=com_pwtseo');

				if (!$oAuth->isAuthenticated())
				{
					$oAuth->authenticate();
				}

				$host = $params->get('domain', $oURI->getHost());
				$url  = 'https://www.googleapis.com/webmasters/v3/sites/' .
					preg_replace('/https?:\/\//i', '', $host) . '/sitemaps';

				$response = $oAuth->query($url);

				if ($response->code === 200)
				{
					$body            = json_decode($response->body);
					$this->aSitemaps = $body->sitemap;
				}
				else
				{
					$this->aSitemaps = false;
				}
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage(
					Text::sprintf('COM_PWTSEO_ERRORS_OAUTH_ERROR', $e->getMessage()),
					'error'
				);
			}
		}

		$bHasPWTSitemap = (bool) ComponentHelper::isInstalled('com_pwtsitemap');

		if ($bHasPWTSitemap)
		{
			// Using JMenu covers unpublished menu-items. Due to complexity, we ignore access setting.
			$this->bHasSitemap
				= (bool) AbstractMenu::getInstance('site')->getItems(
					array(
					'link'
					),
					array(
					'index.php?option=com_pwtsitemap&view=sitemap&layout=sitemapxml&format=xml'
					)
				);
		}

		try
		{
			$this->aDomain = PWTSEOHelper::getDomainInformation();
		}
		catch (Exception $e)
		{
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	private function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_pwtseo');

		JToolbarHelper::title(Text::_('COM_PWTSEO_DASHBOARD_LABEL'), 'pwtseo');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_pwtseo');
		}
	}
}
