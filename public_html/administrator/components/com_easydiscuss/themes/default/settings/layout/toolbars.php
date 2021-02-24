<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_enabletoolbar', 'COM_EASYDISCUSS_ENABLE_TOOLBAR'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarhome', 'COM_ED_ENABLE_TOOLBAR_HOME'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbartags', 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarcategories', 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarusers', 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarbadges', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarprofile', 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarlogin', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_conversation', 'COM_EASYDISCUSS_TOOLBAR_CONVERSATIONS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_notification', 'COM_EASYDISCUSS_TOOLBAR_NOTIFICATIONS'); ?>
					<?php echo $this->html('settings.toggle', 'main_rss', 'COM_EASYDISCUSS_SETTINGS_RSS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_searchbar', 'COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>