<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

// no direct access
defined('_JEXEC') or die;

class Plgwbampcontent extends JPlugin
{
	/**
	 * Build up an array of meta data that can be json_encoded and output
	 * directly to the page
	 *
	 * @param $data
	 * @return array
	 */
	public function onWbampGetJsonldData($context, &$rawJsonLd, $request, $data)
	{
		if ('com_content' != $context)
		{
			return true;
		}

		// start with current
		$jsonld = $rawJsonLd;

		try
		{
			// find article data
			$view = $request->getCmd('view');
			$task = $request->getCmd('task');
			$id = $request->getInt('id');
			if ($view == 'article' && !empty($id) && empty($task))
			{
				// reading publication date the hard way, straight from DB, no other way to get it
				$content = ShlDbHelper::selectObject('#__content', '*', array('id' => $id));

				// published
				$jsonld['datePublished'] = JHtml::_('date', $content->created, DateTime::ATOM, 'UTC');
				if (substr($jsonld['datePublished'], -6) == '+00:00')
				{
					$jsonld['datePublished'] = substr($jsonld['datePublished'], 0, -6) . 'Z';
				}

				// modified
				if ($content->modified == '0000-00-00 00:00:00')
				{
					$jsonld['dateModified'] = $jsonld['datePublished'];
				}
				else
				{
					$jsonld['dateModified'] = JHtml::_('date', $content->modified, DateTime::ATOM, 'UTC');
					if (substr($jsonld['dateModified'], -6) == '+00:00')
					{
						$jsonld['dateModified'] = substr($jsonld['dateModified'], 0, -6) . 'Z';
					}
				}

				// author
				if (!empty($content->created_by_alias))
				{
					$name = $content->created_by_alias;
				}
				else
				{
					// read username from db
					$name = ShlDbHelper::selectResult('#__users', array('name'), array('id' => $content->created_by));
				}

				$jsonld['author'] = array(
					'@type' => 'Person',
					'name' => $name
				);
			}

			//update with our changes
			$rawJsonLd = $jsonld;
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
		}

		return true;
	}
}
