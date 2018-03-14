<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form enctype="multipart/form-data" method="post" class="pointsForm" id="adminForm" name="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BADGES_UPLOAD_CSV_FILES'); ?>
				
				<div class="panel-body">
					<div class="row">
						<div class="pull-right mr-20">
							<a href="https://stackideas.com/docs/easydiscuss/administrators/configuration/mass-assign-badges" target="_blank" class="btn btn-default">
								<?php echo JText::_('View Documentation'); ?>
							</a>
						</div>

						<div class="pull-left ml-20">
							<code>"USER_ID"</code> , <code>"BADGE_ID"</code> , <code>"ACHIEVED_DATE"</code>
						</div>
					</div>

					<div class="mb-20 mt-20">
						<ul class="list-unstyled">
							<li>
								<code>USER_ID</code> - <?php echo JText::_('COM_EASYDISCUSS_BADGES_USER_ID_DESC'); ?>
							</li>
							<li class="mt-5">
								<code>BADGE_ID</code> - <?php echo JText::_('COM_EASYDISCUSS_BADGES_BADGE_ID_DESC'); ?>
							</li>
							<li class="mt-5">
								<code>ACHIEVED_DATE</code> (<?php echo JText::_('COM_EASYDISCUSS_BADGES_OPTIONAL'); ?>) <?php echo JText::_('- Set the achievement date for the users. (Syntax: DD-MM-YYYY)'); ?>
							</li>
						</ul>
					</div>

					<div>
						<input type="file" name="package" id="package" data-uniform />
						<button class="btn btn-sm btn-primary installUpload"><?php echo JText::_('COM_EASYDISCUSS_BADGES_UPLOAD_CSV_FILE')?> &raquo;</button>
					</div>

				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="badges" />
	<input type="hidden" name="task" value="massAssign" />
	<?php echo JHTML::_('form.token'); ?>
</form>