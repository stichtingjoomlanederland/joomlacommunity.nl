<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

EDCompat::renderModalLibrary();
?>
<div id="badges" class="tab-pane">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_BADGES'); ?>	
				<div class="panel-body">
					<div class="o-form-horizontal">
						<div class="o-form-group">
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGES_TITLE'); ?>
							</div>
							<div class="col-md-8">
								<div class="l-stack">
									<?php if ($badges) { ?>
										<?php foreach ($badges as $badge) { ?>
											<?php echo $this->output('admin/user/badge_item', ['badge' => $badge]);?>
										<?php } ?>
									<?php } ?>

									<div class="<?php echo $badges ? 't-hidden' : '';?>">
										<?php echo JText::_('COM_EASYDISCUSS_USER_NO_BADGES_YET');?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>