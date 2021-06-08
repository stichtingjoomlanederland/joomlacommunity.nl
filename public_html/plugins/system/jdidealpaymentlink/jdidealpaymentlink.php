<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

/**
 * Plugin to generate a payment link.
 *
 * @package  JDiDEAL
 * @since    4.5.0
 */
class plgSystemJdidealpaymentlink extends CMSPlugin
{
	/**
	 * Application
	 *
	 * @var    JApplicationSite
	 * @since  4.5.0
	 */
	protected $app;

	/**
	 * Load the stylesheet as the last one in the administrator section.
	 *
	 * @return  void
	 *
	 * @since   4.5.0
	 */
	public function onBeforeRender()
	{
		/** @var HtmlDocument $document */
		$document = Factory::getDocument();

		if (!$this->app->isClient('administrator') || $document->getType() !== 'html')
		{
			$buffer = $document->getBuffer();

			if (is_array($buffer))
			{
				$buffer = ArrayHelper::toString($buffer);
			}

			if (stripos($buffer, 'editor') === false
				|| is_null(stristr($buffer, 'editor'))
				|| in_array($this->app->input->getCmd('format'), ['raw', 'json'])
			)
			{
				return;
			}
		}

		// We need to add our own stylesheet as last because otherwise our icons are overwritten
		$url = Uri::getInstance()->getHost() . Uri::root(true);
		$stylesheetLink = '<link href="//' . $url . '/media/com_jdidealgateway/css/jdidealgateway.css" rel="stylesheet" />';
		$document->addCustomTag($stylesheetLink);
	}

	/**
	 * Find and replace tags with a payment link.
	 *
	 * @return  void
	 *
	 * @since   4.5.0
	 */
	public function onAfterRender()
	{
		// Fix the icon in the WYSIWYG editor
		if (JVERSION < 4 && $this->app->isClient('administrator') && $this->app->input->getCmd('option') !== 'com_rsform')
		{
			// Replace the icon-jdideal to jdicon-jdideal
			$body = $this->app->getBody();
			$body = str_replace('icon-jdideal', 'jdicon-jdideal', $body);
			$this->app->setBody($body);

			return;
		}

		// Do the tag replacement on the frontend
		if (!$this->app->isClient('site'))
		{
			return;
		}

		$body = $this->app->getBody();
		$body = $this->replaceTags($body);
		$this->app->setBody($body);
	}

	/**
	 * Replace the tags.
	 *
	 * @param   string  $text  The body to replace the tags in.
	 *
	 * @return  string  The replaced string.
	 *
	 * @since   4.5.0
	 */
	private function replaceTags($text)
	{
		$regex = '/{jdidealpaymentlink\s([^\}]+)\}/';

		if (!preg_match_all($regex, $text, $matches))
		{
			return $text;
		}

		// URL to use for the link
		$url = '/index.php?option=com_jdidealgateway&view=pay';

		foreach ($matches[0] as $index => $match)
		{
			$tag       = $matches[0][$index];
			$arguments = $this->convertArguments($matches[1][$index]);
			$title     = 'RO Payments';

			foreach ($arguments as $argument)
			{
				[$name, $value] = explode('=', $argument);

				if ($name === 'title')
				{
					$title = str_ireplace('+', ' ', $value);
				}
				else
				{
					$url .= '&' . $name . '=' . $value;
				}
			}

			$link = HTMLHelper::_('link', Route::_($url), $title);
			$text = str_replace($tag, $link, $text);
		}

		return $text;
	}

	/**
	 * Convert the arguments to an array.
	 *
	 * @param   string  $arguments  The list of arguments.
	 *
	 * @return  array  List of arguments.
	 *
	 * @since   4.5.0
	 */
	private function convertArguments(string $arguments): array
	{
		$replaced = preg_match_all('/[a-z]+="[^"]+"/', $arguments, $matches);

		if (!$replaced || $replaced === 0)
		{
			return [];
		}

		$find      = ['"', "'", ' '];
		$replace   = ['', '', '+'];

		return str_replace($find, $replace, $matches[0]);
	}
}
