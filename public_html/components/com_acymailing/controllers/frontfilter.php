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

include(ACYMAILING_BACK.'controllers'.DS.'filter.php');

class FrontfilterController extends FilterController{

	function listing(){
		die('Access not allowed');
	}
	function process(){
		die('Access not allowed');
	}
	function store(){
		die('Access not allowed');
	}
}
?>
