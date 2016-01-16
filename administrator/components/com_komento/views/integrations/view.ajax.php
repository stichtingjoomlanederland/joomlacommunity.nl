<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class KomentoViewIntegrations extends KomentoAdminView
{
	function migrateSettings()
	{
		$ajax = Komento::getAjax();
		$fromTable = Komento::getTable('configs');
		$toTable = Komento::getTable('configs');

		$component = JRequest::getVar('component');
		$currentComponent = JRequest::getVar('currentComponent');
		$fromTable->load($component);
		$toTable->load($currentComponent);
		$toTable->params = $fromTable->params;
		$toTable->store();

		$ajax->success();
		$ajax->send();
	}
}
