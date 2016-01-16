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
?>

<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav pt-10">
		<ul class="nav nav-list">
			<li class="nav-header"><?php echo JText::_( 'COM_KOMENTO_SETTINGS' ); ?></li>
			<li class="active">
				<a href="#main" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_WORKFLOW' ); ?></a>
			</li>

			<li>
				<a href="#antispam" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ANTISPAM' ); ?></a>
			</li>

			<li>
				<a href="#layout" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_LAYOUT' ); ?></a>
			</li>

			<li>
				<a href="#notifications" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_NOTIFICATIONS' ); ?></a>
			</li>

			<li>
				<a href="#activities" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ACTIVITIES' ); ?></a>
			</li>

			<!-- START: Pro Version Only -->
			<li>
				<a href="#attachment" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ATTACHMENT' ); ?></a>
			</li>
			<!-- END: Pro Version Only -->

			<li>
				<a href="#advance" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></a>
			</li>
		</ul>
		</div>
	</div>
	<div class="span10">
		<div class="tab-content">

			<div class="tab-pane active" id="main">
				<?php echo $this->loadTemplate( 'main_bootstrap' );?>
			</div>

			<div class="tab-pane" id="antispam">
				<?php echo $this->loadTemplate( 'antispam_bootstrap' );?>
			</div>

			<div class="tab-pane" id="layout">
				<?php echo $this->loadTemplate( 'layout_bootstrap' );?>
			</div>

			<div class="tab-pane" id="notifications">
				<?php echo $this->loadTemplate( 'notifications_bootstrap' );?>
			</div>

			<div class="tab-pane" id="activities">
				<?php echo $this->loadTemplate( 'activities_bootstrap' );?>
			</div>

			<!-- START: Pro Version Only -->
			<div class="tab-pane" id="attachment">
				<?php echo $this->loadTemplate( 'attachment_bootstrap' );?>
			</div>
			<!-- END: Pro Version Only -->

			<div class="tab-pane" id="advance">
				<?php echo $this->loadTemplate('advance_bootstrap');?>
			</div>
		</div>
	</div>
</div>
