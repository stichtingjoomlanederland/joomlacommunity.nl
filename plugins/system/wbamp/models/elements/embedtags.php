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

class WbampModelElement_Embedtags
{
	private $_scripts  = array();
	private $_renderer = null;

	public function getData($currentData, $renderer)
	{
		$this->_renderer = $renderer;
		$processedContent = $this->embed($currentData);

		// return processed content and possibly required AMP scripts
		$result = array(
			'data' => $processedContent,
			'scripts' => $this->_scripts
		);

		return $result;
	}

	/**
	 * Search an HTML content for tags
	 * to specific content that have AMP version,
	 * such as:
	 * - twitter tweets
	 * - instagrams images
	 * - vine videos
	 * - youtube videos
	 *
	 * Tags format:
	 *
	 * {wbamp_embed type="_type_" attr*=""}
	 *
	 * where
	 * type = twitter | instagram | vine | youtube | carousel
	 *
	 * + various attributes, dependent on the item being processed
	 *
	 * quotes (") are required around each value
	 *
	 * @param $rawContent
	 *
	 * @return mixed
	 */
	private function embed($rawContent)
	{
		if (empty($rawContent))
		{
			return $rawContent;
		}
		$content = $rawContent;

		// 1 : search for user-created tags
		if (WbampHelper_Runtime::$params->get('embed_user_tags'))
		{
			$regex = '#{wbamp\-embed([^}]*)}#ium';
			$content = preg_replace_callback($regex, array($this, '_processEmbededTag'), $content);
		}

		return $content;
	}

	/**
	 * Preg replace callback, identify tags to replace
	 * them with the AMP version
	 *
	 * @param $match
	 * @return string
	 */
	private function _processEmbededTag($match)
	{
		// detect type we can handle
		if (!empty($match[1]))
		{
			$attributes = JUtility::parseAttributes($match[1]);
			$type = empty($attributes['type']) ? '' : $attributes['type'];
			if (array_key_exists($type, WbampHelper_Runtime::$embedTags))
			{
				// proceed to extract tag and replace it
				return $this->_embedTag($type, $attributes);
			}
		}

		return $match[0];
	}

	/**
	 * Process a user content tag and replace it with
	 * its AMP counterpart.
	 *
	 * @param $type
	 * @param $attributes
	 * @return string
	 */
	private function _embedTag($type, $attributes)
	{
		$displayData = array(
			'params' => WbampHelper_Runtime::$params,
			'joomla_config' => WbampHelper_Runtime::$joomlaConfig,
			'data' => $attributes
		);

		return $this->_renderer->buildTag($type, $displayData);
	}

}
