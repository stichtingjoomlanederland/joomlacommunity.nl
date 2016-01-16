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
defined('_JEXEC') or die('Restricted access'); ?>

<div class="content-head">
	<ul class="content-tabs reset-ul">	<li class="active">
			<a href="#activities-komento" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_KOMENTO' ); ?></a>
		</li>
		<li>
			<a href="#activities-jomsocial" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_JOMSOCIAL' );?></a>
		</li>
		<li>
			<a href="#activities-aup" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_AUP' );?></a>
		</li>
		<li>
			<a href="#activities-discuss" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_DISCUSS' );?></a>
		</li>
		<li>
			<a href="#activities-easysocial" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_SUBTAB_EASYSOCIAL' );?></a>
		</li>
	</ul>
</div>

<div class="tab-content">
	<div class="tab-pane active" id="activities-komento">
		<?php echo $this->loadTemplate( 'activities_komento' ); ?>
	</div>

	<div class="tab-pane" id="activities-jomsocial">
		<?php echo $this->loadTemplate( 'activities_jomsocial' ); ?>
	</div>

	<div class="tab-pane" id="activities-aup">
		<?php echo $this->loadTemplate( 'activities_aup' ); ?>
	</div>

	<div class="tab-pane" id="activities-discuss">
		<?php echo $this->loadTemplate( 'activities_discuss' ); ?>
	</div>

	<div class="tab-pane" id="activities-easysocial">
		<?php echo $this->loadTemplate( 'activities_easysocial' ); ?>
	</div>
</div>
