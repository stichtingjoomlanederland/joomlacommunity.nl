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
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
Komento.require().script('migrator.actions').done(function($) {
	$(document).ready(function() {
		$('.tab-pane').implement('Komento.Controller.Migrator.Actions');
	});
});
</script>
<ul class="tab-master reset-ul">
	<li class="active">
		<a href="#main" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_WORKFLOW' ); ?></a>
	</li>

	<li>
		<a href="#antispam" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ANTISPAM' ); ?></a>
	</li>

	<li>
		<a href="#layout" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_LAYOUT' ); ?></a>
	</li>

	<li>
		<a href="#notifications" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_NOTIFICATIONS' ); ?></a>
	</li>

	<li>
		<a href="#activities" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ACTIVITIES' ); ?></a>
	</li>

	<!-- START: Pro Version Only -->
	<li>
		<a href="#attachment" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ATTACHMENT' ); ?></a>
	</li>
	<!-- END: Pro Version Only -->

	<li>
		<a href="#advance" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></a>
	</li>

	<li>
		<a href="#migratesettings" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_MIGRATE_SETTINGS' ); ?></a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="main">
		<?php echo $this->loadTemplate( 'main' );?>
	</div>

	<div class="tab-pane" id="antispam">
		<?php echo $this->loadTemplate( 'antispam' );?>
	</div>

	<div class="tab-pane" id="layout">
		<?php echo $this->loadTemplate( 'layout' );?>
	</div>

	<div class="tab-pane" id="notifications">
		<?php echo $this->loadTemplate( 'notifications' );?>
	</div>

	<div class="tab-pane" id="activities">
		<?php echo $this->loadTemplate( 'activities' );?>
	</div>

	<!-- START: Pro Version Only -->
	<div class="tab-pane" id="attachment">
		<?php echo $this->loadTemplate( 'attachment' );?>
	</div>
	<!-- END: Pro Version Only -->

	<div class="tab-pane" id="advance">
		<?php echo $this->loadTemplate('advance');?>
	</div>

	<div class="tab-pane" id="migratesettings">
		<?php echo $this->loadTemplate('migratesettings');?>
	</div>
</div>
