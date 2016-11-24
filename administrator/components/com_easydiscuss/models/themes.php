<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelThemes extends EasyDiscussAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieves a list of installed themes on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getThemes()
	{
		$path = DISCUSS_THEMES;

		$result	= JFolder::folders( $path , '.', false , true , $exclude = array('.svn', 'CVS' , '.' , '.DS_Store' ) );
		
		$themes	= array();

		// Cleanup output
		foreach ($result as $item) {
			$name = basename($item);

			$obj = ED::getThemeObject($name);

			if ($obj) {
				$obj->featured = false;

				if ($this->config->get('layout_site_theme') == $obj->element) {
					$obj->featured = true;
				}

				$themes[]	= $obj;
			}
			
		}

		return $themes;
	}

	/**
	 * Retrieve the current front end's default template
	 *
	 * @since 4.0.9
	 * @access public
	 */
	public function getDefaultJoomlaTemplate()
	{
		$db = ED::db();

		$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		$query .= ' WHERE ' . $db->nameQuote('home') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();

		return $template;
	}
}
