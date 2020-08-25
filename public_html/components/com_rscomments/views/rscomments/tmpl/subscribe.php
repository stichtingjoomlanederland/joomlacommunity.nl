<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 
JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_NAME');
JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_EMAIL');
JText::script('COM_RSCOMMENTS_INVALID_SUBSCRIBER_EMAIL');
JText::script('COM_RSCOMMENTS_CONSENT_ERROR'); ?>

<div class="rscomments-subscribe-layout <?php if ($this->config->modal == 2) echo 'rscomments-popup-padding'; ?>">
	<div class="container-fluid mt-4">
		<div class="alert" id="subscriber-message" style="display: none;"></div>
		
		<div class="<?php echo RSCommentsAdapterGrid::card(); ?>">
			<div class="card-body">
				<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
					<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
						<div class="control-group">
							<div class="control-label">
								<label for="name"><?php echo JText::_('COM_RSCOMMENTS_NAME'); ?></label>
							</div>
							<div class="controls">
								<input id="subscriber-name" type="text" name="name" value="" size="40" class="input-xxlarge form-control required" /> 
							</div>
						</div>
					</div>
				</div>
				
				<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
					<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
						<div class="control-group">
							<div class="control-label">
								<label for="email"><?php echo JText::_('COM_RSCOMMENTS_EMAIL'); ?></label>
							</div>
							<div class="controls">
								<input id="subscriber-email" type="text" name="email" value="" size="40" class="input-xxlarge required form-control validate-email" /> 
							</div>
						</div>
					</div>
				</div>
				
				<?php if($this->config->consent) { ?>
				<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
					<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
						<div class="controls">
							<label class="checkbox">
								<input type="checkbox" id="consent" class="rsc_chk required" name="consent" value="1" /> 
								<?php echo JText::_('COM_RSCOMMENTS_CONSENT'); ?>
							</label>
						</div>
					</div>
				</div>
				<?php } ?>
			
			</div>
		</div>
	</div>

	<?php if($this->config->modal == 2) { ?>
	<div class="<?php echo RSCommentsAdapterGrid::row(); ?> mt-4 mb-2">
		<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
			<div class="text-right">
				<button class="btn btn-primary" type="button" onclick="RSComments.doSubscribe();"><?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?></button>
				<button type="button" onclick="window.parent.jQuery.magnificPopup.close();" class="btn btn-secondary"><?php echo JText::_('COM_RSCOMMENTS_CLOSE'); ?></button>
			</div>
		</div>
	</div>
	<?php } ?>

	<button type="button" id="rscomm_subscribe" onclick="RSComments.doSubscribe();" style="display:none">&nbsp;</button>
</div>