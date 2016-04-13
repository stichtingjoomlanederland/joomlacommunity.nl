<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
			<div class="panel-head">
				<h6><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_VBULLETIN_PREFIX'); ?></h6>
			</div>

			<div class="panel-body">
				<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_PREFIX'); ?>
				
				<input type="text" name="migrator_vBulletin_prefix" class="form-control form-control-sm t-lg-mr--md" value="" data-ed-vbulletin-prefix />
				<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_EASYDISCUSS_VBULLETIN_NEXT_STEP');?>" data-ed-vbulletin-next/>

			</div>

		</div>
		<div class="panel hide" data-ed-vbulletin-migrator>
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_DETAILS'); ?>

				<div id="option01" class="panel-body">
					<div class="">
						<div class="form-group">
						
							<p><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE');?></p>
							
							<ul>
								<li><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP'); ?></li>
								<li><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE'); ?></li>
							</ul>

							<button class="btn btn-success" data-ed-migrate>
								<?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL'); ?>
							</button>

						</div>
					</div>
				</div>
			</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
	        	<div class="panel-head">
	        		<b><?php echo JText::_('COM_EASYDISCUSS_PROGRESS');?></b>
	                <span data-progress-loading class="eb-loader-o size-sm hide"></span>
	        	</div>

	        	<div class="panel-body">
		        	<div data-progress-status style="overflow:auto; height:98%;max-height: 300px;"><?php echo JText::_('COM_EASYDISCUSS_MIGRATOR_NO_PROGRESS_YET'); ?></div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-head">
	        		<b><?php echo JText::_('COM_EASYDISCUSS_MIGRATOR_STATISTIC');?></b>
	        	</div>

	        	<div class="panel-body">
	        		<div data-progress-stat style="overflow:auto; height:98%;"></div>
	        	</div>
			</div>
	</div>
</div>
