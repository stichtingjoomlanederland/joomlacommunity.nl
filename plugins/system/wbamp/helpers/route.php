<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.5.0.585
 * @date        2016-08-25
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
	public static function realPathToAmp($path, $query = '', $full = true)
	{
		$rawPath = $path;
		if (WbampHelper_Runtime::isStandaloneMode())
		{
			$ampSuffix = '';
		}
		else
		{
			$ampSuffix = WbampHelper_Runtime::$params->get('amp_suffix', 'amp');
		}

		// does it have a query string?
		if (empty($query))
		{
			$questionMarkPosition = JString::strpos($path, '?');
			if ($questionMarkPosition !== false)
			{
				$path = Jstring::substr($path, 0, $questionMarkPosition);
				$query = Jstring::substr($rawPath, $questionMarkPosition);
			}
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
			if (!ShlSystem_Route::isFullyQualified($ampPath))
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
	 * Decides if a given path specification rule applies
	 * to the current request
	 *
	 * Rule specs:
	 * * => any URL
	 * xxxx => exactly 'xxxxx'
	 * xxx?yyy => 'xxx' + any character + 'yyy'
	 * xxx*yyy => 'xxx' + any string + 'yyy'
	 * *xxxx => any string + 'xxxxx'
	 * xxxx* => 'xxxx' + any string
	 * *xxxx* => any string + 'xxxxx' + any string
	 * *xxxx*yyyy => any string + 'xxxxx' + any string + 'yyyy'
	 *
	 * @param string $rule
	 * @param string $path the path relative to the root of the site, starting with a /
	 */
	public static function pathRuleMatch($rule, $path)
	{
		// shortcuts
		if ('*' == $rule)
		{
			return true;
		}

		// build a reg exp based on rule
		if (JString::substr($rule, 0, 1) == '~')
		{
			// this is a regexp, use it directly
			$regExp = $rule;
		}
		else
		{
			// actually build the reg exp
			$saneStarBits = array();
			$starBits = explode('*', $rule);
			foreach ($starBits as $sBit)
			{
				// same thing with ?
				$questionBits = explode('?', $sBit);
				$saneQBit = array();
				foreach ($questionBits as $qBit)
				{
					$saneQBit[] = preg_quote($qBit);
				}

				$saneStarBits[] = implode('?', $saneQBit);
			}

			// each part has been preg_quoted
			$sanitized = implode('*', $saneStarBits);
			$regExp = str_replace('?', '.', $sanitized);
			$regExp = str_replace('*', '.*', $regExp);
			$regExp = '~^' . $regExp . '$~uU';
		}

		// execute and return
		$shouldApply = preg_match($regExp, $path);

		return $shouldApply;
	}
}
