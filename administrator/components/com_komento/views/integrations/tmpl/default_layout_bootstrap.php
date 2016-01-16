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
defined('_JEXEC') or die('Restricted access'); ?>

<h3><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ACTIVITIES' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#layout-general" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_GENERAL' ); ?></a>
			</li>
			<li>
				<a href="#layout-frontpage" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_FRONTPAGE' );?></a>
			</li>
			<li>
				<a href="#layout-comment-item" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_COMMENT_ITEM' );?></a>
			</li>
			<li>
				<a href="#layout-form" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_COMMENT_FORM' );?></a>
			</li>
			<li>
				<a href="#layout-avatars" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_AVATARS' );?></a>
			</li>
			<li>
				<a href="#layout-bbcode" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_BBCODE' );?></a>
			</li>
			<li>
				<a href="#layout-social" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_SOCIAL' );?></a>
			</li>
			<li>
				<a href="#layout-conversation_bar" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_CONVERSATION_BAR' );?></a>
			</li>
			<li>
				<a href="#layout-famelist" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_FAMELIST' );?></a>
			</li>
			<li>
				<a href="#layout-syntaxhighligher" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_SUBTAB_SYNTAXHIGHLIGHTER' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="layout-general">
			<?php echo $this->loadTemplate( 'layout_general_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-frontpage">
			<?php echo $this->loadTemplate( 'layout_frontpage_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-comment-item">
			<?php echo $this->loadTemplate( 'layout_comment_item_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-form">
			<?php echo $this->loadTemplate( 'layout_form_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-avatars">
			<?php echo $this->loadTemplate( 'layout_avatars_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-bbcode">
			<?php echo $this->loadTemplate( 'layout_bbcode_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-social">
			<?php echo $this->loadTemplate( 'layout_social_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-conversation_bar">
			<?php echo $this->loadTemplate( 'layout_conversation_bar_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-famelist">
			<?php echo $this->loadTemplate( 'layout_famelist_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="layout-syntaxhighligher">
			<?php echo $this->loadTemplate( 'layout_syntaxhighlighter_bootstrap' ); ?>
		</div>

	</div>

</div>
