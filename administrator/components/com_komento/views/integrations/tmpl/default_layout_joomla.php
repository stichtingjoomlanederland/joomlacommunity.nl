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

$pane	= JPane::getInstance('Tabs');

echo $pane->startPane("sublayout");
	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_GENERAL' ) , 'general' );
		echo $this->loadTemplate( 'layout_general_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_FRONTPAGE' ) , 'frontpage' );
		echo $this->loadTemplate( 'layout_frontpage_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_COMMENT_ITEM' ) , 'commentitem' );
		echo $this->loadTemplate( 'layout_comment_item_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_COMMENT_FORM' ) , 'form' );
		echo $this->loadTemplate( 'layout_form_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_AVATARS' ) , 'avatars' );
		echo $this->loadTemplate( 'layout_avatars_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_BBCODE' ) , 'bbcode' );
		echo $this->loadTemplate( 'layout_bbcode_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_SOCIAL' ) , 'social' );
		echo $this->loadTemplate( 'layout_social_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_CONVERSATION_BAR' ) , 'conversationbar' );
		echo $this->loadTemplate( 'layout_conversation_bar_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_FAMELIST' ) , 'famelist' );
		echo $this->loadTemplate( 'layout_famelist_joomla' );
	echo $pane->endPanel();

	echo $pane->startPanel( JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_SYNTAXHIGHLIGHTER' ) , 'syntaxhighlighter' );
		echo $this->loadTemplate( 'layout_syntaxhighlighter_joomla' );
	echo $pane->endPanel();
echo $pane->endPane();
