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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_VOTING'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_allowselfvote', 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowquestionvote', 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowvote', 'COM_EASYDISCUSS_ENABLE_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguestview_whovoted', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VIEW_WHO_VOTED'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguest_vote_question', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_QUESTION'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguest_vote_reply', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_REPLY'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>