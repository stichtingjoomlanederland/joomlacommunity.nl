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
	<ul class="content-tabs reset-ul">
		<li class="active">
			<a href="#notifications-mail" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SUBTAB_MAIL' ); ?></a>
		</li>
		<li>
			<a href="#notifications-easysocial" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SUBTAB_EASYSOCIAL' );?></a>
		</li>
	</ul>
</div>

<div class="tab-content">
	<div class="tab-pane active" id="notifications-mail">
		<?php echo $this->loadTemplate( 'notifications_mail' ); ?>
	</div>

	<div class="tab-pane" id="notifications-easysocial">
		<?php echo $this->loadTemplate( 'notifications_easysocial' ); ?>
	</div>
</div>

