<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
$my = JFactory::getUser();
if(empty($my->id)) die('You can not have access to this page, please log in first');

include(ACYMAILING_BACK.'controllers'.DS.'file.php');

class FrontfileController extends FileController
{
	function __construct($config = array()){
		parent::__construct($config);

		$task = JRequest::getString('task');
		if($task != 'select') die('Access not allowed');
	}

	function select(){
		JRequest::setVar('layout', 'select');
		return parent::display();
	}
}
