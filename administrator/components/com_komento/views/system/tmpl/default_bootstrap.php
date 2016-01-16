<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access'); ?>

<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav pt-10">
		<ul class="nav nav-list">
			<li class="nav-header"><?php echo JText::_( 'COM_KOMENTO_SETTINGS' ); ?></li>
			<li class="active">
				<a href="#database" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_DATABASE' ); ?></a>
			</li>
			<li>
				<a href="#profile" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_PROFILE' ); ?></a>
			</li>
			<li>
				<a href="#schema" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SCHEMA' ); ?></a>
			</li>
			<li>
				<a href="#system" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SYSTEM' ); ?></a>
			</li>
			<li>
				<a href="#advance" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></a>
			</li>
		</ul>
		</div>
	</div>
	<div class="span10">
		<div class="tab-content">
			<div class="tab-pane active" id="database">
				<?php echo $this->loadTemplate( 'database_bootstrap' );?>
			</div>

			<div class="tab-pane" id="profile">
				<?php echo $this->loadTemplate( 'profile_bootstrap' );?>
			</div>

			<div class="tab-pane" id="schema">
				<?php echo $this->loadTemplate( 'schema_bootstrap' );?>
			</div>
			<div class="tab-pane" id="system">
				<?php echo $this->loadTemplate( 'system_bootstrap' );?>
			</div>
			<div class="tab-pane" id="advance">
				<?php echo $this->loadTemplate( 'advance_bootstrap' );?>
			</div>
		</div>
	</div>
</div>
