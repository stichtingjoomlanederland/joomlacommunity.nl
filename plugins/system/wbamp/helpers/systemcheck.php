<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.4.2.551
 * @date        2016-07-19
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC'))
{
	die('');
}

class WbampHelper_Systemcheck
{
	const SCOPE = 'plg_wbamp';

	private static $instance;

	private $_config       = null;
	private $_pluginParams = null;
	private $_app          = null;

	public static function updateSystemMessages($pluginParams, $config)
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		self::$instance->_app = JFactory::getApplication();

		// global config
		self::$instance->_config = $config;
		// current state of parameters
		self::$instance->_pluginParams = $pluginParams;

		// run various checks on the system, and possibly
		// store messages, to be displayed later on in
		// the message center
		self::$instance
			->validationWarning()
			->noPageImageFallbackWarning()
			->invalidPageImageFallbackWarning(self::$instance->_config)
			->invalidCustomCSSWarning()
			->checkSEF()
			->checkSystemPluginsOrder()
			->checkPublisherInfoExist()
			->checkImageExists('publisher')
			->checkLogoDimensions('publisher', self::$instance->_config->publisherLogoSize['width'], self::$instance->_config->publisherLogoSize['height'])
			->checkImageExists('site')
			->checkRenderingTemplate();
	}

	/**
	 * Provides an empty default base message
	 * merged with incoming new message values
	 *
	 * @param $msg
	 * @return array
	 */
	private function buildMessage($msg)
	{
		$baseMsg = array(
			'scope' => self::SCOPE,
			'type' => '',
			'sub_type' => '',
			'display_type' => ShlMsg_Manager::DISPLAY_TYPE_INFO,
			'title' => '',
			'body' => '',
			'action' => ShlMsg_Manager::ACTION_CAN_CLOSE
		);

		return array_merge($baseMsg, $msg);
	}

	/**
	 * Display an info reminder, asking to validate pages
	 *
	 * @return $this
	 */
	private function validationWarning()
	{
		$shouldShow = true;
		$msg = $this->buildMessage(
			array(
				'type' => 'wbamp.information',
				'sub_type' => 'validate',
				'title' => JText::_('PLG_SYSTEM_WBAMP_VALIDATION_WARNING'),
				'body' => '',
				'action' => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_7D
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	/**
	 * Display an info reminder, if no page fallback image has been set
	 *
	 * @return $this
	 */
	private function noPageImageFallbackWarning()
	{
		if (WbampHelper_Edition::$id == 'full')
		{
			$fallbackURL = $this->_pluginParams->get('pages_fallback_image', '');
			$shouldShow = empty($fallbackURL);
		}
		else
		{
			$shouldShow = false;
		}

		$msg = $this->buildMessage(
			array(
				'type' => 'wbamp.information',
				'sub_type' => 'no_page_image_fallback',
				'title' => JText::_('PLG_SYSTEM_WBAMP_NO_PAGE_IMAGE_FALLBACK_WARNING'),
				'body' => '',
				'action' => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_7D
			)
		);
		$this->addUnlessNotAcknowledged($shouldShow, $msg);

		return $this;
	}

	/**
	 * Display an info reminder, page fallback image is not large enough
	 *
	 * @return $this
	 */
	private function invalidPageImageFallbackWarning($config)
	{
		$shouldShow = false;
		if (WbampHelper_Edition::$id == 'full')
		{
			$fallbackURL = $this->_pluginParams->get('pages_fallback_image', '');
			if (!empty($fallbackURL))
			{
				$fallbackImage = ShlSystem_Route::absolutify($fallbackURL, true);
				$pageImageSize = array('width' => 0, 'height' => 0);
				$pageImageSize['width'] = $this->_pluginParams->get('pages_fallback_image_width', 0);
				$pageImageSize['height'] = $this->_pluginParams->get('pages_fallback_image_height', 0);
				$pageImageSize = WbampHelper_Media::findImageSizeIfMissing($fallbackImage, $pageImageSize);
				if (empty($pageImageSize['width']) || empty($pageImageSize['height']) || $pageImageSize['width'] < $config->pageImageMinWidth)
				{
					$shouldShow = true;
				}
			}
		}

		$this->cleanAndAdd(
			$shouldShow,
			self::SCOPE,
			'wbamp.config.metadata',
			'invalid_page_image_fallback',
			$title = JText::_('PLG_SYSTEM_WBAMP_NO_PAGE_IMAGE_FALLBACK_ERROR')
		);

		return $this;
	}

	/**
	 * Display a warning if some suspicious CSS is seen in the custom CSS
	 *
	 * @return $this
	 */
	private function invalidCustomCSSWarning()
	{
		$shouldShow = false;
		$invalidCss = array(
			'no_important' => array(
				'reg' => '/\!\s*important/i',
				'msg' => 'PLG_SYSTEM_WBAMP_INVALID_CSS_IMPORTANT_ERROR'
			)
		);

		$msgs = array();
		$customCss = $this->_pluginParams->get('custom_css', '');
		if (!empty($customCss))
		{
			foreach ($invalidCss as $key => $cssRule)
			{
				// simple text found
				if (!empty($cssRule['txt']))
				{
					if (strpos($customCss, $cssRule['txt']) !== false)
					{
						$msgs[] = JText::_($cssRule['msg']);
						$shouldShow = true;
					}
				}

				// reg exp test
				if (!empty($cssRule['reg']))
				{
					if (preg_match($cssRule['reg'], $customCss))
					{
						$msgs[] = JText::_($cssRule['msg']);
						$shouldShow = true;
					}
				}
			}

			$this->cleanAndAdd(
				$shouldShow,
				self::SCOPE,
				'wbamp.config.css',
				'invalid_css_detected',
				$title = JText::_('PLG_SYSTEM_WBAMP_INVALID_CSS_ERROR') . ' ' . implode(', ', $msgs)
			);
		}

		return $this;
	}

	/**
	 * Either Joomla SEF or sh404SEF should be enabled
	 *
	 * @return $this
	 */
	private function checkSEF()
	{
		$shouldShow = !$this->_app->get('sef') && !defined('SH404SEF_IS_RUNNING');

		$this->cleanAndAdd(
			$shouldShow,
			self::SCOPE,
			'wbamp.config.joomla',
			'no_sef',
			$title = JText::_('PLG_SYSTEM_WBAMP_NO_SEF')
		);

		return $this;
	}

	/**
	 * Checks if publisher information are present:
	 * - name
	 * - an image (image validity will be checked later)
	 *
	 * @return $this
	 */
	private function checkPublisherInfoExist()
	{
		$publisherName = JString::trim($this->_pluginParams->get('publisher_name', ''));
		$publisherLogoUrl = JString::trim($this->_pluginParams->get('publisher_image', ''));
		$shouldShow = empty($publisherName) || empty($publisherLogoUrl);

		$this->cleanAndAdd(
			$shouldShow,
			self::SCOPE,
			'wbamp.config.metadat',
			'missing_publisher_information',
			$title = JText::_('PLG_SYSTEM_WBAMP_ERROR_MISSING_PUBLISHER_INFORMATION')
		);

		return $this;
	}

	/**
	 * If a publisher logo has been entered, it must
	 * match rules on its size
	 *
	 * https://developers.google.com/structured-data/carousels/top-stories#logo_guidelines
	 *
	 * @return $this
	 */
	private function checkImageExists($imageType)
	{
		$shouldShow = false;

		// if filled in, the publisher logo must
		// fit within 600px x 60px
		// height should be 60 or width should be 600px
		$logoUrl = $this->_pluginParams->get($imageType . '_image', '');
		if (!empty($logoUrl))
		{
			$logoSize = WbampHelper_Media::getImageSize($logoUrl);
			$width = $this->_pluginParams->get($imageType . '_image_width', $logoSize['width']);
			$height = $this->_pluginParams->get($imageType . '_image_height', $logoSize['height']);
			if (empty($width) || empty($height))
			{
				$shouldShow = true;
			}
		}

		$this->cleanAndAdd(
			$shouldShow,
			self::SCOPE,
			'wbamp.config.metadata',
			'missing_' . $imageType . '_logo',
			$title = JText::_('PLG_SYSTEM_WBAMP_ERROR_MISSING_' . strtoupper($imageType) . '_WIDTH_OR_HEIGHT_TITLE')
				. ' ' . JText::_('PLG_SYSTEM_WBAMP_ERROR_MISSING_IMAGE_WIDTH_OR_HEIGHT')
		);

		return $this;
	}

	/**
	 * If a publisher logo has been entered, it must
	 * match rules on its size
	 *
	 * https://developers.google.com/structured-data/carousels/top-stories#logo_guidelines
	 *
	 * @return $this
	 */
	private function checkLogoDimensions($imageType, $imageWidth, $imageHeight)
	{
		$shouldShow = false;

		// if filled in, the publisher logo must
		// fit within 600px x 60px
		// height should be 60 or width should be 600px
		$logoUrl = $this->_pluginParams->get($imageType . '_image', '');
		if (!empty($logoUrl))
		{
			$logoSize = WbampHelper_Media::getImageSize($logoUrl);
			$width = $this->_pluginParams->get($imageType . '_image_width', $logoSize['width']);
			$height = $this->_pluginParams->get($imageType . '_image_height', $logoSize['height']);
			if (
				(!empty($width) && $width != $imageWidth)
				&&
				(!empty($height) && $height != $imageHeight)
			)
			{
				$shouldShow = true;
			}
		}

		$this->cleanAndAdd(
			$shouldShow,
			self::SCOPE,
			'wbamp.config.metadata',
			'invalid_publisher_logo',
			$title = JText::_('PLG_SYSTEM_WBAMP_ERROR_INVALID_IMAGE_WIDTH_OR_HEIGHT_TITLE')
				. ' ' . JText::_('PLG_SYSTEM_WBAMP_ERROR_INVALID_IMAGE_WIDTH_OR_HEIGHT')
		);

		return $this;
	}

	/**
	 * Check wbamp, shlib and sh404sef plugins order
	 *
	 * @return $this
	 */
	private function checkSystemPluginsOrder()
	{
		$shouldShow = false;

		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')->from('#__extensions')->where($db->qn('type') . '=' . $db->q('plugin'))
			      ->where($db->qn('folder') . '=' . $db->q('system'))
			      ->where($db->qn('enabled') . '=' . $db->q('1'))
			      ->where($db->qn('element') . " in (" . $db->q('shlib') . "," . $db->q('wbamp') . "," . $db->q('sh404sef') . ")")
			      ->order('ordering asc');
			$db->setQuery($query);
			$plugins = $db->loadObjectList('element');

			if (empty($plugins['shlib']) || empty($plugins['wbamp']))
			{
				$shouldShow = true;
			}
			elseif ($plugins['shlib']->ordering >= $plugins['wbamp']->ordering)
			{
				$shouldShow = true;
			}

			// test for sh404SEF
			if (!empty($plugins['sh404sef']) && $plugins['wbamp']->ordering >= $plugins['sh404sef']->ordering)
			{
				$shouldShow = true;
			}

			$this->cleanAndAdd(
				$shouldShow,
				self::SCOPE,
				'wbamp.config.joomla',
				'invalid_plugins_order',
				$title = JText::_('PLG_SYSTEM_WBAMP_ERROR_INVALID_PLUGINS_ORDER'),
				$body = JText::_('PLG_SYSTEM_WBAMP_ERROR_INVALID_PLUGINS_ORDER_BODY')
			);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage('Error: ' . $e->getMessage());
		}

		return $this;
	}

	/**
	 * Warning if wbAMP template not used
	 *
	 * @return $this
	 */
	private function checkRenderingTemplate()
	{
		$shouldShow = false;
		try
		{
			// currenlty set rendering template, can be 'wbamp' if "Use default" is selected
			// or an integer, representing the template id in the #__template_styles table
			$renderingTemplate = (int) $this->_pluginParams->get('rendering_template', 0);

			if (!empty($renderingTemplate))
			{
				// check that the id is that of the wbamp template
				$wbAMPTemplateId = ShlDbHelper::selectResult('#__template_styles', 'id', array('template' => 'wbamp'));
				$shouldShow = $wbAMPTemplateId != $renderingTemplate;
			}

			$msg = $this->buildMessage(
				array(
					'type' => 'wbamp.information',
					'sub_type' => 'rendering_template',
					'title' => JText::_('PLG_SYSTEM_WBAMP_RENDERING_TEMPLATE_WARNING'),
					'body' => '',
					'action' => ShlMsg_Manager::ACTION_ON_CLOSE_DELAY_7D
				)
			);
			$this->addUnlessNotAcknowledged($shouldShow, $msg);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage('Error: ' . $e->getMessage());
		}

		return $this;
	}

	/**
	 * Sugar to displaying/removing a group of messages
	 *
	 * @param $shouldShow
	 * @param $msg
	 * @return $this
	 */
	private function cleanAndAdd($shouldShow, $scope, $type, $subType, $title,
	                             $body = '',
	                             $action = ShlMsg_Manager::ACTION_CAN_CLOSE,
	                             $displayType = ShlMsg_Manager::DISPLAY_TYPE_ERROR)
	{
		// clear previous messages
		ShlMsg_Manager::getInstance()->delete(
			array(
				'scope' => $scope,
				'type' => $type,
				'sub_type' => $subType
			)
		);

		// add if needed
		if ($shouldShow)
		{
			$msg = $this->buildMessage(
				array(
					'scope' => $scope,
					'type' => $type,
					'sub_type' => $subType,
					'title' => $title,
					'body' => $body,
					'action' => $action,
					'display_type' => $displayType
				)
			);

			ShlMsg_Manager::getInstance()->add($msg);
		}

		return $this;
	}

	/**
	 * Sugar to displaying/removing a specific message
	 *
	 * @param $shouldShow
	 * @param $msg
	 * @return $this
	 */
	private function addUnlessNotAcknowledged($shouldShow, $msg)
	{
		if ($shouldShow)
		{
			ShlMsg_Manager::getInstance()->addUnlessNotAcknowledged($msg);
		}
		else
		{
			// clear any non-acknowledged instance
			ShlMsg_Manager::getInstance()->acknowledge(
				array(
					'scope' => $msg['scope'],
					'type' => $msg['type'],
					'sub_type' => $msg['sub_type']
				),
				$force = true
			);
		}

		return $this;
	}
}
