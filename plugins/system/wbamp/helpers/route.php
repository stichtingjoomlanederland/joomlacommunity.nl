<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.3.1.490
 * @date        2016-05-18
 */

defined('_JEXEC') or die;

/**
 * Route helper
 *
 */
class WbampHelper_Route
{

	private static $urlSuffix = null;

	/**
	 * Turn a relative-to-page URL into an absolute one
	 *
	 * @param $url
	 * @param bool $forceDomain if URL is already absolute, we won't fully qualify it with a domain (if relativen we
	 * still prepend the full domain
	 * @param null $isAdmin If === true or === false, disable using JApplication::isAdmin, for testing
	 *
	 * @return string
	 */
	public static function absolutify($url, $forceDomain = false, $isAdmin = null)
	{
		// is it already absolute?
		if (self::isFullyQUalified($url))
		{
			return $url;
		}

		if (substr($url, 0, 1) == '/')
		{
			if ($forceDomain)
			{
				// don't know if the possible site subfolder has been prepended
				$uri = JUri::getInstance();
				$subFolder = $uri->base(true);
				if (substr($url, 0, JString::strlen($subFolder)) == $subFolder)
				{
					$prefix = $uri->tostring(array('scheme', 'host'));
				}
				else
				{
					$prefix = $uri->base();
				}
				$url = JString::rtrim($prefix, '/') . $url;
			}

			return $url;
		}

		// relative URL, make it fully qualified
		$base = JUri::base();
		if ($isAdmin === true || ($isAdmin !== false && JFactory::getApplication()->isAdmin()))
		{
			$base = JString::substr($base, 0, -14);
		}

		return $base . $url;
	}

	/**
	 * Make absolute and routed non-sef URLs found in some content
	 *
	 * @param $buffer
	 * @return mixed
	 */
	static public function sef($buffer)
	{
		// save time
		if (empty($buffer))
		{
			return $buffer;
		}

		// Replace src links.
		$base = JUri::base(true) . '/';

		// Replace index.php URI by SEF URI.
		if (strpos($buffer, 'href="index.php?') !== false)
		{
			preg_match_all('#href="index.php\?([^"]+)"#m', $buffer, $matches);
			foreach ($matches[1] as $urlQueryString)
			{
				$buffer = str_replace('href="index.php?' . $urlQueryString . '"', 'href="' . JRoute::_('index.php?' . $urlQueryString) . '"', $buffer);
			}
			self::checkBuffer($buffer);
		}

		// Check for all unknown protocals (a protocol must contain at least one alpahnumeric character followed by a ":").
		$protocols = '[a-zA-Z0-9\-]+:';
		$attributes = array('href=', 'src=', 'poster=');
		foreach ($attributes as $attribute)
		{
			if (strpos($buffer, $attribute) !== false)
			{
				$regex = '#\s+' . $attribute . '"(?!/|' . $protocols . '|\#|\')([^"]+)"#m';
				$buffer = preg_replace($regex, ' ' . $attribute . '"' . $base . '$1"', $buffer);
				self::checkBuffer($buffer);
			}
		}

		return $buffer;
	}

	/**
	 * Replace the matched tags.
	 *
	 * wb_cache: had to copy this as well, as it is a protected method of the original sef system plugin
	 *
	 * @param   array &$matches An array of matches (see preg_match_all).
	 *
	 * @return  string
	 */
	protected static function route(&$matches)
	{
		$url = $matches[1];
		$url = str_replace('&amp;', '&', $url);
		$route = JRoute::_('index.php?' . $url);

		return 'href="' . $route;
	}

	/**
	 * Turns a path (as returned by JURi getPath()) into
	 * its AMP equivalent. Used to advertise the AMP version
	 * of a regular html page
	 * By default, the fully qualified URL is returned
	 *
	 * @param $path
	 * @param $query
	 * @param bool $full
	 * @return string
	 */
	public static function realPathToAmp($path, $query, $full = true)
	{
		if (WbampHelper_Runtime::isStandaloneMode())
		{
			$ampSuffix = '';
		}
		else
		{
			$ampSuffix = WbampHelper_Runtime::$params->get('amp_suffix', 'amp');
		}

		// does it start with a slash? (but not a protocol relative URL)
		$hasLeadingSlash = substr($path, 0, 1) == '/' && substr($path, 0, 2) != '//';

		// does it end with a slash?
		$hasTrailingSlash = substr($path, -1) == '/';

		// do we have an html suffix?
		$htmlSuffix = self::getUrlSuffix();
		if (substr($path, -5) == $htmlSuffix)
		{
			$ampPath = substr($path, 0, -5) . (empty($ampSuffix) ? '' : '.' . $ampSuffix) . $htmlSuffix;
		}

		// is a slash
		else if ($path == '/')
		{
			$ampPath = $ampSuffix;
		}

		// ends with a slash
		else if ($hasTrailingSlash)
		{
			$ampPath = $path . (empty($ampSuffix) ? '' : $ampSuffix . '/');
		}

		// anything else
		else
		{
			$ampPath = $path . (empty($ampSuffix) ? '' : '/' . $ampSuffix);
		}

		// normalize
		$ampPath = JString::ltrim($ampPath, '/');

		// prepare for return
		if ($full)
		{
			// full URL, make sure the path was not already fully qualified
			if (!self::isFullyQUalified($ampPath))
			{
				// apply index.php
				if (!WbampHelper_Runtime::$joomlaConfig->get('sef_rewrite'))
				{
					$ampPath = 'index.php/' . $ampPath;
				}
				$ampPath = WbampHelper_Runtime::$base . $ampPath;
			}
		}
		else if ($hasLeadingSlash)
		{
			// there was a leading slash, put it back
			$ampPath = '/' . $ampPath;
		}

		return $ampPath . $query;
	}

	/**
	 * Figures out the currently set URL suffix
	 * from Joomla SEF or sh404SEF SEF
	 *
	 * @return string
	 **/
	public static function getUrlSuffix()
	{
		if (is_null(self::$urlSuffix))
		{
			$joomlaHtmlSuffix = WbampHelper_Runtime::$joomlaConfig->get('sef_suffix', '');
			if (!empty($joomlaHtmlSuffix))
			{
				self::$urlSuffix = '.html';
			}
			if (defined('SH404SEF_IS_RUNNING'))
			{
				self::$urlSuffix = Sh404sefFactory::getConfig()->suffix;
			}
		}

		return self::$urlSuffix;
	}

	/**
	 * Setter for the URL suffix, normally only used for testing
	 *
	 * @param $suffix
	 */
	public static function setUrlSuffix($suffix)
	{
		self::$urlSuffix = $suffix;
	}

	/**
	 * Check the buffer.
	 *
	 * @param   string $buffer Buffer to be checked.
	 *
	 * @return  void
	 */
	private static function checkBuffer($buffer)
	{
		if ($buffer === null)
		{
			switch (preg_last_error())
			{
				case PREG_BACKTRACK_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.backtrack_limit)";
					break;
				case PREG_RECURSION_LIMIT_ERROR:
					$message = "PHP regular expression limit reached (pcre.recursion_limit)";
					break;
				case PREG_BAD_UTF8_ERROR:
					$message = "Bad UTF8 passed to PCRE function";
					break;
				default:
					$message = "Unknown PCRE error calling PCRE function";
			}

			throw new RuntimeException('wbAMP: error processing Joomla page: ' . $message);
		}
	}

	/**
	 * Finds if a URL is fully qualified, ie starts with a scheme
	 * Protocal-relative URLs are considered fully qualified
	 *
	 * @param $url
	 * @return bool
	 */
	private static function isFullyQUalified($url)
	{
		$isFullyQualified = substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://' || substr($url, 0, 2) == '//';
		return $isFullyQualified;
	}
}
