<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussGdrpTab
{
	public $key = null;
	public $root = null;
	public $title = null;
	public $items = null;
	public $adapter = null;
	public $path = null;

	public function __construct($adapter, $title = '', $rootPath = '')
	{
		$this->key = $adapter->type;
		$this->title = $title ? $title : JText::_('COM_ED_GDPR_TAB_' . strtoupper($adapter->type));
		$this->root = $rootPath;
		$this->items = array();
		$this->adapter = $adapter;

		$this->path = EasyDiscussGdpr::getUserTempPath($adapter->user) . '/' . $this->key;
	}

	/**
	 * Method to process the html content for each item.
	 *
	 * @since  4.1
	 * @access public
	 */
	public function addItem(EasyDiscussGdprTemplate $item)
	{
		$this->items[] = $item;
	}

	/**
	 * Method to resst the process item ids after the process completed.
	 *
	 * @since  4.1
	 * @access public
	 */
	public function clearIds()
	{
		$this->adapter->setParams('ids', array());
		return true;
	}

	/**
	 * Creates the finalized index.html file for the tab
	 *
	 * @since	4.1
	 * @access	private
	 */
	public function createIndexFile($sidebar)
	{
		$baseUrl = '';
		$sectionTitle = 'COM_ED_GDPR_TAB_' . strtoupper($this->key);
		$sectionDesc = $sectionTitle . '_DESC';

		$contents = $this->getContentsFromTemporaryListingFile();

		$theme = ED::themes();
		$theme->set('baseUrl', $baseUrl);
		$theme->set('sidebar', $sidebar);
		$theme->set('contents', $contents);
		$theme->set('hasBack', false);
		$theme->set('sectionTitle', $sectionTitle);
		$theme->set('sectionDesc', $sectionDesc);

		$output = $theme->output('site/gdpr/template');

		JFile::write($this->path . '/index.html', $output);

		// Delete the temporary listing file
		$tmpFile = $this->getTemporaryListingFileName();

		JFile::delete($tmpFile);
	}

	/**
	 * Finalizes a process from an adapter
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function finalize()
	{
		// we need to tell the lib that this adapater is already finished it job
		$this->adapter->setParams('complete', true);
	}

	/**
	 * Determines if the index file of the tab exists
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function hasIndexFile()
	{
		$path = $this->path . '/index.html';
		$exists = JFile::exists($path);

		return $exists;
	}

	/**
	 * Determines if the tab is already finalized
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function isFinalized()
	{
		$finalized = $this->adapter->getParams('complete', false);

		return $finalized;
	}

	/**
	 * Marks an item as processed
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function markItemProcessed(EasyDiscussGdprTemplate $template)
	{
		$ids = $this->adapter->getParams('ids', array());
		$ids[] = $template->id;

		$this->adapter->setParams('ids', $ids);
	}

	/**
	 * Obtained item id's that are already processed by the system
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getProcessedIds()
	{
		$ids = $this->adapter->getParams('ids', array());

		return $ids;
	}

	/**
	 * Retrieve contents from temporary listing file
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getContentsFromTemporaryListingFile()
	{
		$path = $this->getTemporaryListingFileName();
		$contents = JFile::read($path);

		return $contents;
	}

	/**
	 * Generates a random file name for used as the index.html file
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getTemporaryListingFileName()
	{
		$path = $this->path . '/' . md5($this->key);
		$exists = JFile::exists($path);

		if (!$exists) {
			JFile::write($path, '');
		}

		return $path;
	}

	/**
	 * Method to get the processed items.
	 *
	 * @since  4.1
	 * @access public
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Method to retrieve the path for items in the tab.
	 *
	 * @since  4.1
	 * @access public
	 */
	public function getLink($isRoot = false)
	{
		$link = '';

		if (!$isRoot) {
			$link = '../';
		}

		$link .= $this->key . '/index.html';

		return $link;
	}
}
