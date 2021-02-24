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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.dropdown', 'main_login_provider', 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER', '', 
						array(
							'easysocial' => 'EasySocial',
							'joomla' => 'Joomla',
							'cb' => 'Community Builder'
						)
					);?>

					<?php echo $this->html('settings.dropdown', 'main_login_redirect', 'COM_EASYDISCUSS_SELECT_LOGIN_REDIRECT', '', 
						array(
							'frontpage' => 'COM_EASYDISCUSS_REDIRECT_FRONTPAGE',
							'same.page' => 'COM_EASYDISCUSS_REDIRECT_SAME_PAGE'
						)
					);?>

					<?php echo $this->html('settings.dropdown', 'main_logout_redirect', 'COM_EASYDISCUSS_SELECT_LOGOUT_REDIRECT', '', 
						array(
							'frontpage' => 'COM_EASYDISCUSS_REDIRECT_FRONTPAGE',
							'same.page' => 'COM_EASYDISCUSS_REDIRECT_SAME_PAGE',
							'forums' => 'COM_EASYDISCUSS_REDIRECT_FORUMS'
						)
					);?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>