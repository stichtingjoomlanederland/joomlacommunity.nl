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

class EasyDiscussGdprSection
{
	public $user = null;
	public $key = null;
	public $title = null;
	public $subfolder = null;
	public $tabs = null;
	public $path = null;

	public function __construct(DiscussProfile $user, $name, $title = '', $subfolder = false)
	{
		$this->user = $user;
		$this->key = $name;
		$this->title = $title;
		$this->tabs = array();

		$this->path = EasyDiscussGdpr::getUserTempPath($user);

		if ($subfolder) {
			$this->path .= '/' . $this->key;
		}
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

		$theme = ED::themes();
		$contents = $theme->output('site/gdpr/main');

		$theme = ED::themes();
		$theme->set('baseUrl', $baseUrl);
		$theme->set('sidebar', $sidebar);
		$theme->set('contents', $contents);
		$theme->set('hasBack', false);
		$theme->set('sectionTitle', false);
		$theme->set('sectionDesc', false);

		$output = $theme->output('site/gdpr/template');

		JFile::write($this->path . '/index.html', $output);
	}

	/**
	 * Method to create new tab that associate with the section.
	 *
	 * @since  4.1
	 * @access public
	 */
	public function createTab($adapter, $title = '')
	{
		$rootPath = '';

		// 1. If folder not exists, create it
		if ($this->subfolder) {
			$rootPath = $this->key;
		}

		$tab = new EasyDiscussGdrpTab($adapter, $title, $rootPath);
		$this->tabs[$adapter->type] = $tab;

		return $tab;
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
}
