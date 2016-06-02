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

class WbampModel_Renderer
{
	const AMP_SCRIPTS_VERSION = 0.1;
	const AMP_SCRIPTS_PATTERN = 'https://cdn.ampproject.org/v0/amp-%s-%s.js';

	private $_request       = null;
	private $_isAmpPage     = null;
	private $_postProcessor = null;
	private $_manager       = null;
	private $_scripts       = array();

	private $_userSetTags    = array(
		'doc_type',
		'image',
		'author',
		'publisher',
		'date_published',
		'date_modified'
	);
	private $_extractedImage = null;

	private $_userSetData = array();

	/**
	 * Stores request data
	 *
	 */
	public function __construct($request, $manager, $isAmpPage)
	{
		$this->_request = $request;
		$this->_manager = $manager;
		$this->_isAmpPage = $isAmpPage;
	}

	/**
	 * Builds an array of all data required to display the
	 * AMP page. Suitable for use with JLayouts.
	 *
	 * @return array
	 */
	public function getData()
	{
		$data = array();
		$document = JFactory::getDocument();

		$data['params'] = WbampHelper_Runtime::$params;
		$data['joomla_config'] = WbampHelper_Runtime::$joomlaConfig;

		// essentially so that layouts can add scripts to the page
		$data['renderer'] = $this;

		// joomla-rendered content needs to be reprocessed
		$this->_postProcessor = new WbampModel_Postprocess($this->_isAmpPage, $args = array());
		$data['post_processor'] = $this->_postProcessor;

		// default meta data set
		$data['metadata'] = array(
			'title' => $document->getTitle(),
			'description' => $document->getDescription(),
			'keywords' => $document->getMetaData('keywords'),
			'robots' => '',
			'ogp' => '',
			'tcards' => '',
			'tweet_via' => $data['params']->get('tweet_via', ''),
			'publisher_id' => $data['params']->get('publisher_id')
		);

		// sh404SEF integration
		$data['sh404sef_custom_data'] = array();
		if (WbampHelper_Edition::$id == 'full')
		{
			if (WbampHelper_Runtime::isStandaloneMode())
			{
				// update sh404SEF shURL, but only in standalone mode. In other modes,
				// sh404SEF is doing it itself
				Sh404sefHelperShurl::updateShurls();
			}
			WbampHelper_Sh404sef::processMetaData($data, $document, $this->_manager);
		}

		// page data
		$data['canonical'] = $this->_manager->getCanonicalUrl();
		$data['shURL'] = $this->_manager->getShURL();
		$data['amp_url'] = JUri::current();

		// headers
		$this->getHeaders($data);

		// custom content
		$data['custom_style'] = $data['params']->get('custom_css', '');
		$data['custom_links'] = $data['params']->get('custom_links', '');

		// wbamp theme and joomla template used
		$data['theme'] = $data['params']->get('global_theme', 'default.default');
		$templateId = (int) $data['params']->get('rendering_template', 0);
		$data['joomla_template'] = WbampHelper_Media::getTemplateName($templateId);

		// collect page elements data, for later rendering
		$data['navigation_menu'] = $this->getElementData('navigation', $data, '');
		$data['social_buttons'] = $this->getElementData('socialbuttons', $data, array('types' => array(), 'theme' => 'colors', 'style' => 'rounded'));

		// collect main Joomla rendered component content
		$rawContent = JFactory::getDocument()->getBuffer('component');

		// extract content from tags set by user in original content (and remove them)
		$rawContent = $this->getUserSetData($rawContent);
		$data['user_set_data'] = $this->_userSetData;

		// convert standard HTML to AMP compliant HTML
		$data['main_content'] = $this->_postProcessor->convert($rawContent);

		// process all links in content, to make them absolute and SEF if needed
		$data['main_content'] = WbampHelper_Route::sef($data['main_content']);

		// remove tags only used when the regular HTML page is displayed
		$data['main_content'] = str_replace('{wbamp-no-scrub}', '', $data['main_content']);

		// process social networks tags, or raw URLs
		$data['main_content'] = $this->getElementData('embedtags', $data['main_content'], $data['main_content']);

		// header
		$data['header_module'] = $this->renderModule($data['params']->get('header_module'));
		$data['header_module'] = $this->getElementData('embedtags', $data['header_module'], $data['header_module']);
		$data['header_module'] = $this->_postProcessor->convert($data['header_module']);

		$data['site_name'] = $data['params']->get('site_name', '');
		$data['site_link'] = $data['joomla_config']->get('live_site', JUri::base());
		$data['site_image'] = $data['params']->get('site_image', '');
		$data['site_image_size'] = array();
		$data['site_image_size']['width'] = (int) $data['params']->get('site_image_width', 0);
		$data['site_image_size']['height'] = (int) $data['params']->get('site_image_height', 0);
		$data['site_image_size'] = WbampHelper_Media::findImageSizeIfMissing($data['site_image'], $data['site_image_size']);

		// footer, a custom HTML module created by user
		$data['footer'] = $this->renderModule($data['params']->get('footer_module'));
		$data['footer'] = $this->_postProcessor->convert($data['footer']);

		// user notification
		// @TODO: move to method
		$data['user-notification'] = array(
			'text' => $data['params']->get('notification_text', ''),
			'button' => $data['params']->get('notification_button', ''),
			'theme' => $data['params']->get('notification_theme', 'light')
		);
		if (!empty($data['user-notification']) && !empty($data['user-notification']['text']))
		{
			$script = sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, 'user-notification', WbampModel_Renderer::AMP_SCRIPTS_VERSION);
			$this->addScripts(
				array(
					'amp-user-notification' => $script)
			);
		}

		// turn links found in user generated content, marked with a wbamp-link class, into their AMP equivalent
		$data['navigation_menu'] = $this->_postProcessor->ampifyLinks($data['navigation_menu'], $this);
		$data['main_content'] = $this->_postProcessor->ampifyLinks($data['main_content'], $this);
		$data['header_module'] = $this->_postProcessor->ampifyLinks($data['header_module'], $this);
		$data['footer'] = $this->_postProcessor->ampifyLinks($data['footer'], $this);

		// protect email addresses against bots
		$data['main_content'] = $this->_postProcessor->applyFilters($data['main_content']);
		$data['header_module'] = $this->_postProcessor->applyFilters($data['header_module']);
		$data['footer'] = $this->_postProcessor->applyFilters($data['footer']);

		// insert analytics AMP element
		$data['analytics_data'] = $this->getElementData('analytics', $data);

		// let plugins build json-ld data
		$data['json-ld'] = $this->getJsonldData($data);

		// collect additional scripts to insert
		$data['amp_scripts'] = $this->getScripts();

		return $data;
	}

	public function postProcessPage($pageContent)
	{
		return $this->_postProcessor->postProcessPage($pageContent);
	}

	/**
	 * Instantiate an element-specific renderer model
	 * and use its getData() method to collect
	 * some piece of content
	 *
	 * @param $element
	 * @param $currentData
	 * @return array
	 */
	private function getElementData($element, $currentData, $default = array())
	{
		if (WbampHelper_Edition::$id == 'full')
		{
			$name = 'WbampModelElement_' . ucfirst(str_replace('-', '', $element));
			$element = new $name();
			$result = $element->getData($currentData, $this);
			$data = isset($result['data']) ? $result['data'] : array();
			$this->addScripts(isset($result['scripts']) ? $result['scripts'] : array());
		}
		else
		{
			$data = $default;
		}

		return $data;
	}

	public function buildTag($type, $displayData)
	{
		$tag = ShlMvcLayout_Helper::render('wbamp.tags.' . $type, $displayData, WbampHelper_Runtime::$layoutsBasePaths);
		// finally add script to execute the tag
		$this->addScripts(
			array(
				WbampHelper_Runtime::$embedTags[$type]['amp_tag'] => sprintf(WbampModel_Renderer::AMP_SCRIPTS_PATTERN, WbampHelper_Runtime::$embedTags[$type]['script'], WbampModel_Renderer::AMP_SCRIPTS_VERSION)
			)
		);

		return $tag;
	}

	/**
	 * Builds an array of raw headers, to be output
	 * at rendering
	 *
	 * @param $data
	 */
	private function getHeaders(&$data)
	{
		$data['headers'] = array(
			'X-amphtml: wbAMP'
		);
		if (!empty($data['shURL']))
		{
			// add header for shortURL, mostly for HEAD requests
			$data['headers'][] = 'Link: <' . $data['shURL'] . '>; rel=shortlink';
		}
		if ($data['params']->get('adv-gzip', 0))
		{
			$maxAge = $data['params']->get('adv-max-age', '');
			if ($maxAge != '')
			{
				$data['headers'][] = 'Cache-control: max-age=' . (int) $maxAge . ', must-revalidate';
			}
		}
	}

	/**
	 * Extract and store meta data set by user using
	 * {wbamp-*} tags in the content
	 *
	 * Typically: link and size of image
	 *
	 * @param $content
	 */
	private function getUserSetData($content)
	{
		$regex = '#{wbamp\-meta([^}]*)}#m';
		$content = preg_replace_callback($regex, array($this, '_processUserSetData'), $content);

		return $content;
	}

	private function _processUserSetData($match)
	{
		// detect type we can handle
		if (!empty($match[1]))
		{
			$attributes = JUtility::parseAttributes($match[1]);
			$type = empty($attributes['name']) ? '' : $attributes['name'];
			if (in_array($type, $this->_userSetTags))
			{
				$this->_userSetData[$type] = $attributes;
			}
			return '';
		}

		return $match[0];
	}

	/**
	 * Use Joomla to render a module identified by its id
	 * System-non chrome used when rendering, ie no chrome at all
	 *
	 * @param int $moduleId
	 * @return mixed|string
	 */
	public function renderModule($moduleId)
	{
		$renderedModule = '';
		try
		{
			if (!empty($moduleId))
			{
				$moduleData = ShlDbHelper::selectObject('#__modules', array('module', 'title'), array('id' => $moduleId));
				if (!empty($moduleData))
				{
					$module = JModuleHelper::getModule($moduleData->module, $moduleData->title);
					$attribs['style'] = 'System-none';
					$renderedModule = JModuleHelper::renderModule($module, $attribs);
					$renderedModule = WbampHelper_Route::sef($renderedModule);
					$renderedModule = str_replace('{wbamp_current_year}', date('Y'), $renderedModule);

					// make sure module content is AMP compliant
					$renderedModule = $this->_postProcessor->convert($renderedModule);
				}
			}
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
			$renderedModule = '';
		}
		return $renderedModule;
	}

	/**
	 * Build up an array of meta data that can be json_encoded and output
	 * directly to the page
	 *
	 * @param $data
	 * @return array
	 */
	private function getJsonldData($data)
	{
		$jsonld = array();

		try
		{
			$config = new WbampModel_Config();

			// global meta data
			// Item global meta data
			$jsonld['@context'] = 'http://schema.org';
			$defaultArticleType = WbampHelper_Runtime::$params->get('default_doc_type', 'news');
			$jsonld['@type'] = array_key_exists($defaultArticleType, $config->documentTypes) ?
				$config->documentTypes[$defaultArticleType] : $config->documentTypes['news'];
			$jsonld['mainEntityOfPage'] = $this->_manager->getCanonicalUrl();
			$jsonld['headline'] = JFactory::getDocument()->getTitle();

			// publisher
			$publisherImageUrl = WbampHelper_Runtime::$params->get('publisher_image', '');
			$publisherImageSize = array();
			$publisherImageSize['width'] = (int) WbampHelper_Runtime::$params->get('publisher_image_width', 0);
			$publisherImageSize['height'] = (int) WbampHelper_Runtime::$params->get('publisher_image_height', 0);
			$publisherImageSize = WbampHelper_Media::findImageSizeIfMissing($publisherImageUrl, $publisherImageSize);
			$jsonld['publisher'] = array(
				'@type' => 'Organization',
				'name' => WbampHelper_Runtime::$params->get('publisher_name', ''),
				'logo' => array(
					'@type' => 'ImageObject',
					'url' => WbampHelper_Route::absolutify($publisherImageUrl, true),
					'width' => $publisherImageSize['width'],
					'height' => $publisherImageSize['height']

				)
			);

			// let plugins provide basic data
			$status = JPluginHelper::importPlugin('wbamp');
			if ($status)
			{
				$option = $this->_request->getCmd('option', '');
				$eventArgs = array(
					$option,
					& $jsonld,
					$this->_request,
					$data
				);
				ShlSystem_Factory::dispatcher()
				                 ->trigger('onWbampGetJsonldData', $eventArgs);
			}
			else
			{
				throw new Exception('Unable to load wbAMP components support plugins.');
			}

			// then look for overrides set by user in content or otherwise (sh404SEF for instance)

			// publication date: {wbamp-meta name="date_published" content="2016-03-11 06:00:00"}
			if (!empty($data['user_set_data']) && !empty($data['user_set_data']['date_published']) && !empty($data['user_set_data']['date_published']['content']))
			{
				try
				{
					$tz = JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset'));
					$jsonld['datePublished'] = JHtml::_('date', $data['user_set_data']['date_published']['content'] . $tz, DateTime::ATOM, 'UTC');
					if (substr($jsonld['datePublished'], -6) == '+00:00')
					{
						$jsonld['datePublished'] = substr($jsonld['datePublished'], 0, -6) . 'Z';
					}
				}
				catch (Exception $e)
				{
					ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
					if (isset($jsonld['datePublished']))
					{
						unset($jsonld['datePublished']);
					}
				}
			}

			// modification date: {wbamp-meta name="date_modified" content="2016-03-11 06:00:00"}
			if (!empty($data['user_set_data']) && !empty($data['user_set_data']['date_modified']) && !empty($data['user_set_data']['date_modified']['content']))
			{
				try
				{
					$tz = JFactory::getUser()->getParam('timezone', JFactory::getConfig()->get('offset'));
					$jsonld['dateModified'] = JHtml::_('date', $data['user_set_data']['date_modified']['content'] . $tz, DateTime::ATOM, 'UTC');
					if (substr($jsonld['dateModified'], -6) == '+00:00')
					{
						$jsonld['dateModified'] = substr($jsonld['dateModified'], 0, -6) . 'Z';
					}
				}
				catch (Exception $e)
				{
					ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
					if (isset($jsonld['dateModified']))
					{
						unset($jsonld['dateModified']);
					}
				}
			}

			// document type: wbamp-meta name="doc_type"
			if (!empty($data['user_set_data']) && !empty($data['user_set_data']['doc_type']) && !empty($data['user_set_data']['doc_type']['content']))
			{
				$jsonld['@type'] = JString::trim($data['user_set_data']['doc_type']['content']);
			}

			// author: {wbamp-meta name="author" type="Person" content="Yannick Gaultier"}
			if (!empty($data['user_set_data']) && !empty($data['user_set_data']['author']))
			{
				if (!empty($data['user_set_data']['author']['type']))
				{
					$jsonld['author']['@type'] = JString::trim($data['user_set_data']['author']['type']);
				}
				if (!empty($data['user_set_data']['author']['content']))
				{
					$jsonld['author']['name'] = Jstring::trim($data['user_set_data']['author']['content']);
				}
			}

			// we can try find those set in sh404SEF.
			// However, we don't have width/height for those, meaning
			// we have to extract them from the file.
			if (empty($jsonld['image']) && !empty($data['sh404sef_custom_data']))
			{
				$image = empty($data['sh404sef_custom_data']->og_image) ? Sh404sefFactory::getConfig()->ogImage : $data['sh404sef_custom_data']->og_image;
				if (!empty($image))
				{
					$dimensions = WbampHelper_Media::getImageSize($image);
					$jsonld['image'] = array(
						'@type' => 'ImageObject',
						'url' => WbampHelper_Route::absolutify($image, true),
						'width' => $dimensions['width'],
						'height' => $dimensions['height']

					);
				}
			}

			// image, if set by user in regular content, {wbamp-meta name="image" url="" height="123" width="456"}
			if (!empty($data['user_set_data']) && !empty($data['user_set_data']['image']))
			{
				$userSetImage = WbampHelper_Route::absolutify($data['user_set_data']['image']['url'], true);
				$pageImageSize = array();
				$pageImageSize['width'] = empty($data['user_set_data']['image']['width']) ? 0 : $data['user_set_data']['image']['width'];
				$pageImageSize['height'] = empty($data['user_set_data']['image']['height']) ? 0 : $data['user_set_data']['image']['height'];
				$pageImageSize = WbampHelper_Media::findImageSizeIfMissing($userSetImage, $pageImageSize);
				$jsonld['image'] = array(
					'@type' => 'ImageObject',
					'url' => $userSetImage,
					'width' => $pageImageSize['width'],
					'height' => $pageImageSize['height'],

				);
			}

			// fallback to finding an image automatically if none set
			if (empty($jsonld['image']))
			{
				$jsonld['image'] = $this->_findImageIncontent($data['main_content']);
			}

			// finally resort to fallback setting, if any
			if (WbampHelper_Edition::$id == 'full' && empty($jsonld['image']))
			{
				$fallbackImage = $data['params']->get('pages_fallback_image', '');
				if (!empty($fallbackImage))
				{
					$fallbackImage = WbampHelper_Route::absolutify($data['params']->get('pages_fallback_image', ''), true);
					$pageImageSize = array('width' => 0, 'height' => 0);
					$pageImageSize['width'] = $data['params']->get('pages_fallback_image_width', 0);
					$pageImageSize['height'] = $data['params']->get('pages_fallback_image_height', 0);
					$pageImageSize = WbampHelper_Media::findImageSizeIfMissing($fallbackImage, $pageImageSize);
					if (!empty($pageImageSize['width']) && !empty($pageImageSize['height']) && $pageImageSize['width'] >= $config->pageImageMinWidth)
					{
						$jsonld['image'] = array(
							'@type' => 'ImageObject',
							'url' => $fallbackImage,
							'width' => $pageImageSize['width'],
							'height' => $pageImageSize['height'],

						);
					}
				}
			}
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
			$jsonld = array();
		}

		return $jsonld;
	}

	/**
	 * Extract and store meta data set by user using
	 * {wbamp-*} tags in the content
	 *
	 * Typically: link and size of image
	 *
	 * @param $content
	 */
	private function _findImageIncontent($content)
	{
		$regex = '#<amp\-img([^>]*)>#Uum';
		preg_replace_callback($regex, array($this, '_extractImageFromIncontent'), $content);

		return $this->_extractedImage;
	}

	/**
	 * Stores the first image found in content to be used
	 * as page image
	 *
	 * @param $match
	 * @return mixed
	 */
	private function _extractImageFromIncontent($match)
	{
		// detect type we can handle
		if (!empty($match[1]) && empty($this->_extractedImage))
		{
			$attributes = JUtility::parseAttributes($match[1]);
			$imageUrl = WbampHelper_Route::absolutify($attributes['src'], true);
			$imageSize = array();
			$imageSize['width'] = empty($attributes['width']) ? 0 : $attributes['width'];
			$imageSize['height'] = empty($attributes['height']) ? 0 : $attributes['height'];
			$imageSize = WbampHelper_Media::findImageSizeIfMissing($imageUrl, $imageSize);

			// only insert image if we think it's ok, ie have dimensions and min width is ok
			$config = new WbampModel_Config();
			if (!empty($imageSize['width']) && !empty($imageSize['height']) && $imageSize['width'] >= $config->pageImageMinWidth)
			{
				$this->_extractedImage = array(
					'@type' => 'ImageObject',
					'url' => $imageUrl,
					'width' => $imageSize['width'],
					'height' => $imageSize['height']

				);
			}
		}

		return $match[0];
	}

	/**
	 * Collect all scripts added by renderer or postprocessor
	 *
	 * @return array
	 */
	public function getScripts()
	{
		$this->_scripts = array_merge($this->_scripts, (array) $this->_postProcessor->getScripts());
		return $this->_scripts;
	}

	/**
	 * Add an amp tag handler script definition
	 * to the list of scripts to load in the page
	 *
	 * @param $scripts
	 */
	private function addScripts($scripts)
	{
		$this->_scripts = array_merge($this->_scripts, (array) $scripts);
	}
}
