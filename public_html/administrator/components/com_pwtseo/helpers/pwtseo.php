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
use Joomla\CMS\Factory;
use Joomla\CMS\Http\Http;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * PWTSEO helper for the backend.
 *
 * @since    1.0
 */
class PWTSEOHelper
{
	/**
	 * Render submenu.
	 *
	 * @param   string $vName The name of the current view.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSEO_DASHBOARD_LABEL'),
			'index.php?option=com_pwtseo',
			$vName == 'pwtseo'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSEO_ARTICLES_LABEL'),
			'index.php?option=com_pwtseo&view=articles',
			$vName == 'articles'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSEO_CUSTOM_LABEL'),
			'index.php?option=com_pwtseo&view=customs',
			$vName == 'customs'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSEO_MENUS_LABEL'),
			'index.php?option=com_pwtseo&view=menus',
			$vName == 'menus'
		);

		JHtmlSidebar::addEntry(
			Text::_('COM_PWTSEO_DATALAYER_LABEL'),
			'index.php?option=com_pwtseo&view=datalayers',
			$vName == 'datalayers'
		);
	}

	/**
	 * Returns a human readable version of the given context
	 *
	 * @param   string $sContext The context to get the label for
	 *
	 * @return  string The human readable label
	 *
	 * @since   1.0
	 */
	public static function getContextLabel($sContext)
	{
		$aArr = array(
			'com_content.article' => Text::_('COM_PWTSEO_CONTEXT_CONTENT_ARTICLES_LABEL')
		);

		return isset($aArr[$sContext]) ? $aArr[$sContext] : '';
	}

	/**
	 * Returns the ID of the PWT SEO plugin on this system
	 *
	 * @return  integer The ID or 0 on failure
	 *
	 * @since   1.2.0
	 */
	public static function getPlugin()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('client_id') . ' = 0')
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('element') . ' = ' . $db->quote('pwtseo'));

		try
		{
			return $db->setQuery($query)->loadResult();
		}
		catch (Exception $e)
		{
		}

		return 0;
	}

	/**
	 * @param   string $text      The text of which the words should be counted
	 * @param   string $blacklist Optional string with words that should be ignored
	 * @param   int    $max       Max number of words to return
	 *
	 * @return  array The list of most common words, sorted by occurrence
	 *
	 * @since   1.3.0
	 */
	public static function getMostCommenWords($text, $blacklist = '', $max = 15)
	{
		// Remove most common tags and html
		$text = array_count_values(explode(' ', preg_replace('/{+?.*?}+?|\.|:/i', ' ', strip_tags($text))));

		uasort(
			$text,
			function ($a, $b) {
				return $b - $a;
			}
		);

		foreach ($text as $word => $count)
		{
			if (stripos($blacklist, $word) !== false)
			{
				unset($text[$word]);
			}
		}

		return array_slice(array_keys($text), 0, $max);
	}

	/**
	 * Get general information about a domain like amount of pages that have been indexed
	 *
	 * @return  array An array with data separated for Google and Bing
	 *
	 * @since   1.3.1
	 */
	public static function getDomainInformation()
	{
		$params       = ComponentHelper::getParams('com_pwtseo');
		$googleDomain = $params->get('google_domain', 'www.google.com');

		$http = new Http;
		$site = Uri::getInstance()->getHost();

		// Get amount of pages that have been crawled
		$response = $http->get('https://' . $googleDomain . '/search?q=site%3A' . urlencode($site));
		preg_match('/id="resultStats".*?([0-9]+)/', $response->body, $googlePages);

		// Get amount of backlinks
		$response = $http->get('https://' . $googleDomain . '/search?q="' . urlencode($site . '" -site:' . $site) . '&filter=0&gws_rd=cr');
		preg_match('/id="resultStats".*?([0-9]+)/', $response->body, $googleBacklinks);

		return array(
			'google' => array(
				'pages'     => is_array($googlePages) && isset($googlePages[1]) ? $googlePages[1] : 0,
				'backlinks' => is_array($googleBacklinks) && isset($googleBacklinks[1]) ? $googleBacklinks[1] : 0
			)
		);
	}

	/**
	 * Get the ranking for a key-phrase.
	 *
	 * @param   string $keyphrase The key-phrase to check
	 * @param   string $path      If restriction to a certain url should be applied, otherwise only domain will be checked
	 *
	 * @return  array  Returns the first match found with rank and url that was found
	 */
	public static function getRankingForKeyPhrase($keyphrase, $path = '')
	{
		$ret = array(
			'rank' => 0
		);

		$params       = ComponentHelper::getParams('com_pwtseo');
		$googleDomain = $params->get('google_domain', 'www.google.com');
		$thisDomain   = Uri::getInstance()->getHost();

		$http = new Http;

		// Anything above 100 we just ignore
		$response = $http->get('https://' . $googleDomain . '/search?q=' . urlencode($keyphrase) . '&num=100');
		$reader   = new DOMDocument;

		// Suppress errors because google might give invalid html
		@$reader->loadHTML(mb_convert_encoding($response->body, 'HTML-ENTITIES', 'UTF-8'));

		$readerX = new DOMXPath($reader);
		$url     = Uri::getInstance();

		// Because the first couple of hits might be from youtube, we ignore those
		$youtube = 0;

		foreach ($readerX->query('//div //div //div //a[starts-with(@href, \'/url?q=\')][not(ancestor::span)]') as $i => $result)
		{
			preg_match('/q=(.*?)\&/', $result->getAttribute('href'), $href);

			if ($href && isset($href[1]))
			{
				$url->parse($href[1]);

				if ($url->getHost() === 'www.youtube.com')
				{
					$youtube++;
				}

				if ($url->getHost() === $thisDomain)
				{
					if ($path && stripos($href[1], $path) === false)
					{
						continue;
					}

					$ret = array(
						'rank' => (++$i) - $youtube,
						'url'  => $href[1]
					);

					break;
				}
			}
		}

		return $ret;
	}
}
