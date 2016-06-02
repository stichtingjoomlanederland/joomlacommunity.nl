<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.3.1.490
 * @date        2016-05-18
 */

// no direct access
defined('_JEXEC') or die;

class WbampModel_Manager
{
	private $_router        = null;
	private $_originalUri   = null;
	private $_originalPath  = '';
	private $_query         = '';
	private $_newPath       = '';
	private $_canonicalUrl  = '';
	private $_shUrl         = '';
	private $_uri           = null;
	private $_ampSuffixes   = array();
	private $_currentSuffix = '';

	private $_isAmpRequest = false;

	/**
	 * Stores incoming router and request, as provided
	 * by Joomla during the BEFORE_PROCESS stage of parsing
	 *
	 * @param $router
	 * @param $uri
	 */
	public function __construct(&$router, &$uri, $rawUri)
	{
		$this->_router = $router;
		$this->_uri = $uri;
		$this->_originalUri = clone($uri);
		$this->_originalPath = str_replace(WbampHelper_Runtime::$basePath, '', $rawUri->getPath());

		// remove possible leading index.php
		if (!WbampHelper_Runtime::$joomlaConfig->get('sef_rewrite', 0))
		{
			$this->_originalPath = substr($this->_originalPath, 0, 10) == '/index.php' ?
				substr($this->_originalPath, 11) : $this->_originalPath;
		}

		// prepare list of suffixes
		$ampSuffix = WbampHelper_Runtime::$params->get('amp_suffix', 'amp');
		$this->_ampSuffixes = array(
			array('suffix' => $ampSuffix, 'replacer' => ''),
			array('suffix' => $ampSuffix . '/', 'replacer' => '/')
		);

		// get the set URL suffix, usually 'html'
		$urlSuffix = WbampHelper_Route::getUrlSuffix();
		if (!empty($urlSuffix))
		{
			$this->_ampSuffixes[] = array(
				'suffix' => '.' . $ampSuffix . $urlSuffix, 'replacer' => $urlSuffix
			);
		}
	}

	/**
	 * Parses the original URI, finds if it is an AMP
	 * requests and updates the URI accordingly
	 *
	 * @return bool
	 */
	public function parseAndUpdateRequest()
	{
		$hasTrailingSlash = substr($this->_originalPath, -1) == '/';
		if (WbampHelper_Runtime::isStandaloneMode())
		{
			// standalone
			$this->_isAmpRequest = true;
			$this->_newPath = $this->_uri->getPath();
		}
		else
		{
			// look for one of the possible suffixes at the end of the incoming URL
			foreach ($this->_ampSuffixes as $suffixId => $suffix)
			{
				$fullSuffix = substr($suffix['suffix'], 0, 1) == '.' ? $suffix['suffix'] : '/' . $suffix['suffix'];
				if (
					$this->_originalPath == $suffix['suffix']  // home page
					|| substr($this->_originalPath, -strlen($fullSuffix)) == $fullSuffix
				)
				{
					$this->_isAmpRequest = true;
					$this->_currentSuffix = $fullSuffix;
					// NB: we use here $this->_uri->getpath() instead of $this->_originalPath
					// because $this->_uri->getpath() has already been urldecoded by the router
					// and this is what we need to set back in the router for futher processing
					// however the JURI class incorrectly remove the trailing slash in incoming
					// URLs, so we have to compensate for that
					$currentPath = $this->_uri->getPath() . ($hasTrailingSlash ? '/' : '');
					$this->_newPath = substr($currentPath, 0, -strlen($fullSuffix)) . $suffix['replacer'];
					break;
				}
			}
		}

		if ($this->_isAmpRequest)
		{
			//mimic Jomla incorrect behavior, which suppresses trailing slash
			//$hasTrailingSlash = substr($this->_newPath, -1) == '/';
			$truncatedPath = $hasTrailingSlash ? JString::ltrim($this->_newPath, '/') : JString::trim($this->_newPath, '/');

			$this->_uri->setPath($truncatedPath);
			$this->_query = $this->_uri->getQuery();
			$this->_query = empty($this->_query) ? '' : '?' . $this->_query;
			$urlRewritePrefix = 'index.php';
			if (defined('SH404SEF_IS_RUNNING') && empty($truncatedPath))
			{
				// sh404SEF: on home page, we don't keep the index.php bit as Joomla does
				$urlRewritePrefix = '';
			}

			if (!WbampHelper_Runtime::$joomlaConfig->get('sef_rewrite'))
			{
				// not using URL Rewriting, stick index.php at beginning of new URL
				$truncatedPath = $urlRewritePrefix . (empty($truncatedPath) ? '' : '/' . $truncatedPath);
			}

			// find the canonical based on the previously calculated bits
			// not forgetting to also re-append the trailing slash Joomla stripped
			$this->_canonicalUrl = WbampHelper_Runtime::$base . $truncatedPath . ($hasTrailingSlash ? '/' : '') . $this->_query;
		}

		// return true if an AMP page
		return $this->_isAmpRequest;
	}

	/**
	 * Turns a path (as returned by JURi getPath()) into
	 * its AMP equivalent. Used to advertise the AMP version
	 * of a regular html page
	 * By default, the fully qualified URL is returned
	 *
	 * @param string $path If empty, defaults to current page
	 * @param bool $full
	 * @return string
	 */
	public function getAMPUrl($path = '', $full = true)
	{
		if (empty($path))
		{
			$path = $this->_originalPath;
			$query = $this->_query;
		}
		else
		{
			$query = '';
		}

		return WbampHelper_Route::realPathToAmp($path, $query, $full);
	}

	/**
	 * Get back the original URI as passed by Joomla
	 *
	 * @return JUri
	 */
	public function getOriginalUri()
	{
		return $this->_originalUri;
	}

	/**
	 * Get back the processed URI
	 *
	 * @return JUri
	 */
	public function getUri()
	{
		return $this->_uri;
	}

	/**
	 * Getter for current page status
	 *
	 * @return bool
	 */
	public function isAMPRequest()
	{
		return $this->_isAmpRequest;
	}

	/**
	 * Get the canonical URL computed
	 * based on the AMP URL requested
	 * Empty if not an AMP request.
	 *
	 * @return string
	 */
	public function getCanonicalUrl()
	{
		return $this->_canonicalUrl;
	}

	/**
	 * Setter for canonical URL
	 * @param $url
	 * @return $this
	 */
	public function setCanonicalUrl($url)
	{
		$this->_canonicalUrl = $url;

		return $this;
	}

	/**
	 * If sh404SEF is running, pick up the shURL
	 * for the current (full HTML) page
	 *
	 * @return mixed
	 */
	public function getShURL()
	{
		if (empty($this->_shUrl))
		{
			if (defined('SH404SEF_IS_RUNNING')
				&& Sh404sefFactory::getConfig()->insertShortlinkTag
			)
			{
				$this->_shUrl = Sh404sefFactory::getPageInfo()->shURL;
				$this->_shUrl = empty($this->_shUrl) ? '' : WbampHelper_Route::absolutify($this->_shUrl, true);
			}
		}

		return $this->_shUrl;
	}

	/**
	 * Setter for short url
	 *
	 * @param $url
	 * @return $this
	 */
	public function setShURL($url)
	{
		$this->_shUrl = $url;

		return $this;
	}
}
