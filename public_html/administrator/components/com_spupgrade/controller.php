<?php

/**
 * @package		SP Upgrade
 * @subpackage	Components
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		http://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of SPUpgrade component
 */
class SPUpgradeController extends JControllerLegacy
{
    
    protected $default_view = 'tables';
    
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = Array())
	{

		// call parent behavior
		parent::display($cachable);

	}
}
