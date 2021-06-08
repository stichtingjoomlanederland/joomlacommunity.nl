<?php
/**
 * @package         Regular Labs Extension Manager
 * @version         7.4.9
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2021 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController as JController;

/**
 * Master Display Controller
 */
class RegularLabsManagerController extends JController
{
	/**
	 * @var        string    The default view.
	 */
	protected $default_view = 'default';
}
