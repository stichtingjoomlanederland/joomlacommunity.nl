<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$pane = JPane::getInstance( 'Tabs' );

echo $pane->startPane( 'sublayout' );
	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_KOMENTO' ), 'komento' );
		echo $this->loadTemplate( 'activities_komento_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_JOMSOCIAL' ), 'jomsocial' );
		echo $this->loadTemplate( 'activities_jomsocial_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_AUP' ), 'aup' );
		echo $this->loadTemplate( 'activities_aup_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_DISCUSS' ), 'discuss' );
		echo $this->loadTemplate( 'activities_discuss_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_EASYSOCIAL' ), 'easysocial' );
		echo $this->loadTemplate( 'activities_easysocial_joomla' );
	echo $pane->endPanel();
echo $pane->endPane();
