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

class WbampModel_Postprocess
{
	private $_args      = array();
	private $_isAmpPage = null;

	private $_scripts = array();

	private $_processorsFiles = array(
		// vendor WP
		'/vendors/wp/includes/sanitizers/class-amp-audio-sanitizer.php',
		'/vendors/wp/includes/sanitizers/class-amp-base-sanitizer.php',
		'/vendors/wp/includes/sanitizers/class-amp-iframe-sanitizer.php',
		'/vendors/wp/includes/sanitizers/class-amp-img-sanitizer.php',
		'/vendors/wp/includes/sanitizers/class-amp-video-sanitizer.php',
		'/vendors/wp/includes/utils/class-amp-dom-utils.php',
		'/vendors/wp/includes/utils/class-amp-html-utils.php',
	);

	private $_sanitizers = array(
		'AMP_Img_Sanitizer' => array(),
		'AMP_Video_Sanitizer' => array(),
		'AMP_Audio_Sanitizer' => array(),
		'AMP_Iframe_Sanitizer' => array(
			'add_placeholder' => true,
		)
	);

	/**
	 * Store user params and global Joomla config
	 */
	public function __construct($isAmpPage, $args = array())
	{
		$this->_isAmpPage = $isAmpPage;
		$this->args = $args;
	}

	/**
	 * Apply global postprocessing to the full page
	 * already rendered, ready to be displayed
	 *
	 * @param $rawPage
	 */
	public function ampifyLinks($content, $renderer)
	{
		// create a DOM object
		$dom = WbampHelper_Dom::fromContent($content);

		// search for links marked as wbamp-link
		$modified = false;
		$links = $dom->getElementsByTagName('a');
		$linksCount = $links->length;
		if (empty($linksCount))
		{
			return $content;
		}

		for ($i = $linksCount - 1; $i >= 0; $i--)
		{
			$link = $links->item($i);
			$attributes = AMP_DOM_Utils::get_node_attributes_as_assoc_array($link);

			if (!array_key_exists('href', $attributes))
			{
				continue;
			}

			$shouldAmpify = false;

			// automatic processing for Joomla prev/next links
			if (WbampHelper_Runtime::$params->get('ampify_pagination') && !empty($attributes['rel']) && ($attributes['rel'] == 'prev' || $attributes['rel'] == 'next'))
			{
				// are we in a joomla pagination ul?
				$parent = $link->parentNode;
				if (!empty($parent))
				{
					$grandParent = $parent->parentNode;
					if (!empty($grandParent) && $grandParent->tagName == 'ul')
					{
						$class = $grandParent->getAttribute('class');
						$shouldAmpify = strpos($class, 'pagenav') !== false;
					}
				}
			}

			// link was marked by user
			if (!$shouldAmpify && array_key_exists('class', $attributes))
			{
				$shouldAmpify = strpos($attributes['class'], 'wbamp-link') !== false;
			}

			// process if we should
			if ($shouldAmpify)
			{
				// replace that link
				$href = $link->getAttribute('href');
				$href = WbampHelper_Route::realPathToAmp($href, '', false);
				$link->setAttribute('href', $href);
				$modified = true;
			}

			// identify specific links we want to autolink, or rather auto tag:
			if (WbampHelper_Edition::$id == 'full')
			{
				$autoTagger = new WbampModelElement_Autotag();
				$autoTagged = $autoTagger->autotag($dom, $link, $attributes, $renderer);
				if ($autoTagged)
				{
					$modified = true;
				}
			}
		}

		if ($modified)
		{
			// rebuild page from DOM
			$content = WbampHelper_Dom::fromDom($dom);
		}

		return $content;
	}

	/**
	 * Convert an HTML fragment to AMP specs
	 *
	 * @param $rawContent
	 * @return mixed|string
	 */
	public function convert($rawContent)
	{
		if (empty($rawContent))
		{
			return $rawContent;
		}

		// load multiples sanitizer files
		$this->_loadProcessors();

		// create a DOM object
		$dom = WbampHelper_Dom::fromContent($rawContent);

		// filter out unwanted stuff
		$dom = $this->_cleanContent($rawContent, $dom);

		// convert to AMP tags, mostly through black list
		$dom = $this->_convertToAmp($dom);

		// final cleanup is whitelist-based
		$wlCleaner = new WbampModelProcessor_Whitelist(new WbampModel_Config());
		$dom = $wlCleaner->sanitize($dom);

		$convertedContent = WbampHelper_Dom::fromDom($dom);

		return $convertedContent;
	}

	/**
	 * Collect all scripts added by various plugins
	 *
	 * @return array
	 */
	public function getScripts()
	{
		return $this->_scripts;
	}

	/**
	 * Apply final content filters on content
	 *
	 * Note: this must be applied late in the process
	 * on raw text, as processing through DOM object
	 * would revert the encoding to regular UTF-8 chars
	 *
	 * @param $rawContent
	 * @return mixed
	 */
	public function applyFilters($rawContent)
	{
		if (empty($rawContent))
		{
			return $rawContent;
		}

		$content = $rawContent;

		// email obfuscation
		if(WbampHelper_Runtime::$params->get('email_protection', true))
		{
			$obfuscator = new WbampModelProcessor_Obfuscator();
			$obfuscator->process($content);
		}

		// more unusual stuff
		$content = $this->filterHtml($content);

		return $content;
	}

	/**
	 * Handle some unusual cases of HTML that needs
	 * specific processing for AMP
	 *
	 * @param $content
	 * @return mixed
	 */
	private function filterHtml($content)
	{
		/**
		 * We must use LIBXML_NOEMPTYTAG when collecting back our page content
		 * from the DOMDocument used to process it, because the AMP runtime
		 * has issues with self-closing tags (https://github.com/ampproject/amphtml/issues/360
		 * and https://github.com/ampproject/amphtml/issues/362)
		 *
		 * This causes <br> or <br /> in content to be output as
		 * <br></br>, which should be OK but is interpreted as
		 * 2 consecutives <br /> tags instead of one by at least Chrome and Firefox (04/2016)
		 *
		 * We choose to simply replace those instances with self-closing tag
		 * which does not seem to be an issue for the AMP runtime.
		 *
		 */
		$content = str_replace('<br></br>', '<br />', $content);

		/**
		 * Same kind of stuff: saveWml() convert end-of-line into &#13;
		 */
		$content = str_replace('&#13;', '', $content);

		return $content;
	}

	/**
	 * Remove/replace any Joomla-specific tags or content
	 * that could break AMP
	 * Also remove user-marked content, using
	 * {wbamp hide_on_amp start} and {wbamp hide_on_amp end} tags
	 *
	 * @param $rawContent
	 * @return mixed
	 */
	private function _cleanContent($rawContent, $dom)
	{
		if (empty($rawContent))
		{
			return $rawContent;
		}
		$content = $rawContent;

		// remove content marked for deletion by content creator
		$regExp = '#{wbamp-hide start}.*{wbamp-hide end}#iuUs';
		$content = preg_replace($regExp, '', $content, -1, $replaceCount);
		$modified = !empty($replaceCount);

		// Legacy remove content marked for deletion by content creator
		$regExp = '#{wbamp hide_on_amp start}.*{wbamp hide_on_amp end}#iuUs';
		$content = preg_replace($regExp, '', $content, -1, $replaceCount);
		$modified = $modified || !empty($replaceCount);

		// remove tags around content that should only be displayed on AMP pages
		$toRemoves = array('{wbamp-show start}', '{wbamp-show end}');
		foreach ($toRemoves as $toRemove)
		{
			if (strpos($content, $toRemove) !== false)
			{
				$content = str_replace($toRemove, '', $content);
				$modified = true;
			}
		}

		// remove sh404SEF social buttons
		if (defined('SH404SEF_IS_RUNNING'))
		{
			// raw tags
			$regExp = '#{sh404sef_social_buttons[^}]*}#Uu';
			$content = preg_replace($regExp, '', $content, -1, $replaceCount);
			$modified = $modified || !empty($replaceCount);

			// automatic tags
			$regExp = '#<!-- sh404SEF social buttons.*End of sh404SEF social buttons -->#Uus';
			$content = preg_replace($regExp, '', $content, -1, $replaceCount);
			$modified = $modified || !empty($replaceCount);
		}

		// update the DOM object, only if we modified the content
		if ($modified)
		{
			// rebuild a DOM object
			$dom = WbampHelper_Dom::fromContent($content);
		}

		return $dom;
	}

	/**
	 * Remove/replace html tags to comply with AMP specs
	 *
	 * @param $rawContent
	 * @return string
	 */
	private function _convertToAmp($dom)
	{
		foreach ($this->_sanitizers as $sanitizer_class => $args)
		{
			$sanitizer = new $sanitizer_class($dom, array_merge($this->_args, $args));

			if (!is_subclass_of($sanitizer, 'AMP_Base_Sanitizer'))
			{
				ShlSystem_Log::error('wbamp', 'Unable to load AMP building classes, AMP plugin cannot run.');
				continue;
			}

			$sanitizer->sanitize();
			$this->addScripts($sanitizer->get_scripts());
		}

		return $dom;
	}

	/**
	 * Loads WP amp plugin classes, with a small set of ompatibility functions
	 */
	private function _loadProcessors()
	{
		$basePath = JPATH_ROOT . '/plugins/system/wbamp';

		// WP compat layer
		include_once $basePath . '/helpers/wp_compat.php';

		// actual files to include

		foreach ($this->_processorsFiles as $file)
		{
			include_once $basePath . $file;
		}
	}

	/**
	 * Add an amp tag handler script definition
	 * to the list of scripts to load in the page
	 *
	 * @param $scripts
	 */
	private function addScripts($scripts)
	{
		$this->_scripts = array_merge($this->_scripts, $scripts);
	}
}
