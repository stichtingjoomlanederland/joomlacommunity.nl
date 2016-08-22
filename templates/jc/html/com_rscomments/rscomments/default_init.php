<?php
/**
 * @package RSComments!
 * @copyright (C) 2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access'); ?>

<?php if ($this->config->terms) { ?>
	<div id="rscomments-terms" class="modal fade">
		<div class="modal-dialog">
			 <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo JText::_('COM_RSCOMMENTS_TERMS_AND_CONDITIONS'); ?></h3>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<a href="javascript:void(0)" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSCOMMENTS_CLOSE'); ?></a>
				</div>
			 </div>
		</div>
	</div>
<?php } ?>

<?php if ($this->config->enable_subscription && !RSCommentsHelper::isSubscribed($this->id, $this->option) && !$this->user->get('id')) { ?>
	<div id="rscomments-subscribe" class="modal fade">
		<div class="modal-dialog">
			 <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?></h3>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<input type="hidden" id="commentoption" name="commentoption" value="<?php echo $this->option; ?>" />
					<input type="hidden" id="commentid" name="commentid" value="<?php echo $this->id; ?>" />
					<button class="btn btn-primary" type="button" onclick="rscomments_subscribe();"><?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?></button>
					<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSCOMMENTS_CLOSE'); ?></button>
				</div>
			 </div>
		</div>
	</div>
<?php } ?>

<?php if ($this->config->enable_reports) { ?>
	<div id="rscomments-report" class="modal fade">
		 <div class="modal-dialog">
			 <div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo JText::_('COM_RSCOMMENTS_REPORT'); ?></h3>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<input type="hidden" id="commentid" name="commentid" />
					<button class="btn btn-primary" type="button" onclick="rscomments_report();"><?php echo JText::_('COM_RSCOMMENTS_REPORT'); ?></button>
					<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSCOMMENTS_CLOSE'); ?></button>
				</div>
			 </div>
		 </div>
	</div>
<?php } ?>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			<?php if ($this->config->enable_location) { ?>RSCLocation.init();<?php } ?>
			jQuery('.rscomments .hasTooltip').css('display','');
		});
		initTooltips();
		<?php if (!RSCommentsHelper::getThreadStatus($this->id,$this->option)) { ?>rsc_reset_form();<?php } ?>
		<?php if($this->config->enable_smiles == 1) { ?>document.onclick=rsc_check;<?php echo "\n"; } ?>
	</script>

<?php if (!RSCommentsHelper::isJ3()) { ?>
	<style type="text/css">
		[id^="rscomment_tmp"] {
			margin-top: 30px;
		}
	</style>
<?php } ?>