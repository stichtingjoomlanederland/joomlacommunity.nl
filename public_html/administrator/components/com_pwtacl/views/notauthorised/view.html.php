<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\MVC\View\HtmlView;

// No direct access.
defined('_JEXEC') or die;

/**
 * NotAuthorized HTML view class.
 *
 * @since   3.0
 */
class PwtaclViewNotauthorised extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string $tpl Template
	 *
	 * @return  mixed
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function display($tpl = null)
	{
		// Display the view
		return parent::display($tpl);
	}
}
