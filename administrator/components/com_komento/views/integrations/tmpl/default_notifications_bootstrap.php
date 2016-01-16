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

<h3><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_NOTIFICATIONS' );?></h3>
<hr />
<div class="row-fluid">

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#notifications-mail" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SUBTAB_MAIL' ); ?></a>
			</li>
			<li>
				<a href="#notifications-easysocial" data-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_NOTIFICATION_SUBTAB_EASYSOCIAL' );?></a>
			</li>
		</ul>

	</div>

	<div class="tab-content">

		<div class="tab-pane active" id="notifications-mail">
			<?php echo $this->loadTemplate( 'notifications_mail_bootstrap' ); ?>
		</div>

		<div class="tab-pane" id="notifications-easysocial">
			<?php echo $this->loadTemplate( 'notifications_easysocial_bootstrap' ); ?>
		</div>

	</div>

</div>
