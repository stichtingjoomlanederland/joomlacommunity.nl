<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<?php 
	if ($this->config->terms) {
		$footer = '<a href="javascript:void(0)" class="btn btn-primary" onclick="rscomments_agree();">'.JText::_('COM_RSCOMMENTS_I_AGREE').'</a><a href="javascript:void(0)" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</a>';
		$tURL = JRoute::_('index.php?option=com_rscomments&task=terms&tmpl=component', false);
		echo JHtml::_('bootstrap.renderModal', 'rscomments-terms', array('title' => JText::_('COM_RSCOMMENTS_TERMS_AND_CONDITIONS'), 'url' => $tURL, 'footer' => $footer, 'bodyHeight' => '70'));
		echo '<input type="hidden" name="rscomments_terms" value="'.$tURL.'" />';
	}
	
	if ($this->config->enable_subscription && !RSCommentsHelper::isSubscribed($this->id, $this->theoption) && !$this->user->get('id')) {
		$footer = '<input type="hidden" id="commentoption" name="commentoption" value="'.$this->theoption.'" /><input type="hidden" id="commentid" name="commentid" value="'.$this->id.'" /><button class="btn btn-primary" type="button" onclick="jQuery(\'#rscomments-subscribe iframe\').contents().find(\'#rscomm_subscribe\').click();">'.JText::_('COM_RSCOMMENTS_SUBSCRIBE').'</button><button type="button" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</button>';
		$sURL = JRoute::_('index.php?option=com_rscomments&task=subscribe&tmpl=component&theoption='.$this->theoption.'&id='.$this->id, false);
		echo JHtml::_('bootstrap.renderModal', 'rscomments-subscribe', array('title' => JText::_('COM_RSCOMMENTS_SUBSCRIBE'), 'url' => $sURL, 'footer' => $footer, 'bodyHeight' => '70'));
		echo '<input type="hidden" name="rscomments_subscribe" value="'.$sURL.'" />';
	}
	
	if ($this->config->enable_reports) {
		$footer = '<input type="hidden" id="reportid" name="reportid" value="" /><button class="btn btn-primary" type="button" onclick="rsc_do_report();">'.JText::_('COM_RSCOMMENTS_REPORT').'</button><button type="button" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</button>';
		$rURL = JRoute::_('index.php?option=com_rscomments&task=report&tmpl=component', false);
		echo JHtml::_('bootstrap.renderModal', 'rscomments-report', array('title' => JText::_('COM_RSCOMMENTS_REPORT'), 'url' => $rURL, 'footer' => $footer, 'bodyHeight' => '70'));
		echo '<input type="hidden" name="rscomments_report" value="'.$rURL.'" />';
	}
	
	$cURL = JRoute::_('index.php?option=com_rscomments&task=mycomments&tmpl=component', false);
	echo JHtml::_('bootstrap.renderModal', 'rscomments-mycomments', array('title' => JText::_('COM_RSCOMMENTS_MY_COMMENTS'), 'url' => $cURL, 'bodyHeight' => '70'));
	echo '<input type="hidden" name="rscomments_comments" value="'.$cURL.'" />';
?>

<script type="text/javascript">
jQuery(document).ready(function() {
	<?php if ($this->config->enable_location) { ?>RSCLocation.init();<?php } ?>
	jQuery('.rscomments .hasTooltip').css('display','');
});
initTooltips();
<?php if (!RSCommentsHelper::getThreadStatus($this->id,$this->theoption)) { ?>rsc_reset_form();<?php } ?>
<?php if($this->config->enable_smiles == 1) { ?>document.onclick=rsc_check;<?php echo "\n"; } ?>
</script>