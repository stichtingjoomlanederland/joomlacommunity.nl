<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 
JText::script('COM_RSCOMMENTS_REPORT_NO_REASON');
JText::script('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA');
JText::script('COM_RSCOMMENTS_CONSENT_ERROR'); ?>

<div class="rscomments-report-layout <?php if ($this->config->modal == 2) echo 'rscomments-popup-padding'; ?>">
	<div class="container-fluid mt-4">
		<div class="alert" id="report-message" style="display: none;"></div>

		<div class="<?php echo RSCommentsAdapterGrid::card(); ?>">
			<div class="card-body">
				<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
					<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
						<div class="control-group">
							<div class="control-label">
								<label for="name"><?php echo JText::_('COM_RSCOMMENTS_REPORT_REASON'); ?></label>
							</div>
							<div class="controls">
								<textarea id="report-reason" name="report" class="span11 form-control" cols="45" rows="7"></textarea>
							</div>
						</div>
					</div>
				</div>
					
				<?php if ($this->config->enable_captcha_reports) { ?>
				<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
					<div class="<?php echo RSCommentsAdapterGrid::column(12); ?>">
						<div class="control-group">
							<div class="controls rsc-captcha-container">
								<div class="<?php echo RSCommentsAdapterGrid::column(5); ?>">
									<?php if ($this->config->captcha == 0) { ?>
									<img src="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=report&hash='.md5(time())); ?>" alt="" height="80" />
										<span class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_REFRESH_CAPTCHA_DESC')); ?>">
											<a class="rscomments-refresh-captcha" style="border-style: none" href="javascript:void(0)" onclick="RSComments.captcha(this, '<?php echo $this->root.RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=report'); ?>');">
												<i class="fa fa-refresh"></i>
											</a>
										</span> <br />
										<input type="text" name="captcha" size="40" value="" class="inputbox form-control <?php echo RSTooltip::tooltipClass(); ?> required" title="<?php echo RSTooltip::tooltipText($this->config->captcha_cases ? JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_SENSITIVE') : JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_INSENSITIVE')); ?>" />
									<?php } else { ?>
									<div id="rsc-g-recaptcha-report"></div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
					
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
				<button class="btn btn-primary" type="button" onclick="RSComments.doReport();"><?php echo JText::_('COM_RSCOMMENTS_REPORT'); ?></button>
				<button type="button" onclick="window.parent.jQuery.magnificPopup.close();" class="btn btn-secondary"><?php echo JText::_('COM_RSCOMMENTS_CLOSE'); ?></button>
			</div>
		</div>
	</div>
	<?php } ?>

	<button type="button" id="rscomm_report" onclick="RSComments.doReport();" style="display:none">&nbsp;</button>
</div>