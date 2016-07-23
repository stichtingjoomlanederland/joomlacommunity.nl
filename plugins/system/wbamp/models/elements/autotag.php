<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

class WbampModelElement_Autotag
{
	private $_modified = false;

	public function autotag($dom, $link, $attributes, $renderer)
	{
		// identify specific links we want to autolink to:
		if (WbampHelper_Runtime::$params->get('embed_auto_link') && !empty($attributes['href']))
		{
			foreach (WbampHelper_Runtime::$embedTags as $tagName => $tagRecord)
			{
				if (!empty($tagRecord['url_regexp']) && preg_match($tagRecord['url_regexp'], $attributes['href'], $matches))
				{
					// replace the current link with an AMP tag
					$method = 'get' . ucfirst($tagName) . 'UrlData';
					$newTag = $renderer->buildTag($tagName, WbampHelper_Tags::$method($matches));

					if (!empty($newTag))
					{
						// insert new tag
						$fragment = $dom->createDocumentFragment();
						$fragment->appendXML($newTag);
						$parent = $link->parentNode;
						$parent->insertBefore($fragment, $link);
						$parent->removeChild($link);

						// mark as modified, to update the DOM object
						$this->_modified = true;

						// we have replaced the link with an amp tag,
						// don't keep trying to do it again
						break;
					}
				}
			}
		}

		return $this->_modified;
	}
}
