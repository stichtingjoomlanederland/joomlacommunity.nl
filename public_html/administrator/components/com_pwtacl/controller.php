<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * PWT ACL Controller
 *
 * @since   3.0
 */
class PwtAclController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var     $default_view
	 * @since   3.0
	 */
	protected $default_view = 'dashboard';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BaseController
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = [])
	{
		// Get needed language files
		PwtAclHelper::getLanguages();

		return parent::display();
	}
}
