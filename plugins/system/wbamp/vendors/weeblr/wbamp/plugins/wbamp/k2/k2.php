<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

class PlgwbampK2 extends JPlugin
{
	private $_cache = array();

	/**
	 * Build up an array of meta data that can be json_encoded and output
	 * directly to the page
	 *
	 * @param $data
	 * @return array
	 */
	public function onWbampGetJsonldData($context, &$rawJsonLd, $request, $data)
	{
		if ('com_k2' != $context)
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
			$layout = $request->getCmd('layout');
			$id = $request->getInt('id');
			if ($view == 'item' && (is_null($layout) || $layout == 'item') && !empty($id) && empty($task))
			{

				// reading publication date the hard way, straight from DB, no other way to get it
				if (!isset($this->_cache[$id]))
				{
					$this->_cache[$id] = ShlDbHelper::selectObject('#__k2_items', '*', array('id' => $id));
				}

				// published
				$jsonld['datePublished'] = JHtml::_('date', $this->_cache[$id]->created, DateTime::ATOM, 'UTC');
				if (substr($jsonld['datePublished'], -6) == '+00:00')
				{
					$jsonld['datePublished'] = substr($jsonld['datePublished'], 0, -6) . 'Z';
				}

				// modified
				if ($this->_cache[$id]->modified == '0000-00-00 00:00:00')
				{
					$jsonld['dateModified'] = $jsonld['datePublished'];
				}
				else
				{
					$jsonld['dateModified'] = JHtml::_('date', $this->_cache[$id]->modified, DateTime::ATOM, 'UTC');
					if (substr($jsonld['dateModified'], -6) == '+00:00')
					{
						$jsonld['dateModified'] = substr($jsonld['dateModified'], 0, -6) . 'Z';
					}
				}

				// author
				if (!empty($this->_cache[$id]->created_by_alias))
				{
					$name = $this->_cache[$id]->created_by_alias;
				}
				else
				{
					// read username from db
					$name = ShlDbHelper::selectResult('#__users', array('name'), array('id' => $this->_cache[$id]->created_by));
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

	/**
	 * Finds an item category
	 * @param $context
	 * @param $itemId
	 * @param $currentCatId
	 * @param $request
	 * @return bool
	 */
	public function onWbampGetCategoryFromItem($context, $itemId, &$currentCatId, $request)
	{
		if ('com_k2' != $context || empty($itemId))
		{
			return true;
		}

		// start with current
		$catid = $currentCatId;

		try
		{
			// reading publication date the hard way, straight from DB, no other way to get it
			if (!isset($this->_cache[$itemId]))
			{
				$this->_cache[$itemId] = ShlDbHelper::selectObject('#__k2_items', '*', array('id' => $itemId));
			}
			if (!empty($this->_cache[$itemId]))
			{
				$catid = $this->_cache[$itemId]->catid;
			}

			//update with our changes
			$currentCatId = $catid;
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
		}
	}
}
