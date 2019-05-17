<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

$tURL = JRoute::_('index.php?option=com_rscomments&task=terms&tmpl=component', false);
$sURL = JRoute::_('index.php?option=com_rscomments&task=subscribe&tmpl=component', false);
$rURL = JRoute::_('index.php?option=com_rscomments&task=report&tmpl=component', false);
$cURL = JRoute::_('index.php?option=com_rscomments&task=mycomments&tmpl=component', false);

if ($this->config->terms && $this->config->modal == 1) {
	$footer = '<a href="javascript:void(0)" class="btn btn-primary" onclick="RSComments.agree();">'.JText::_('COM_RSCOMMENTS_I_AGREE').'</a><a href="javascript:void(0)" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</a>';
	echo JHtml::_('bootstrap.renderModal', 'rscomments-terms', array('title' => JText::_('COM_RSCOMMENTS_TERMS_AND_CONDITIONS'), 'url' => $tURL, 'footer' => $footer, 'bodyHeight' => '70'));
}

if ($this->config->enable_subscription && $this->config->modal == 1) {
	$footer = '<button class="btn btn-primary" type="button" onclick="jQuery(\'#rscomments-subscribe iframe\').contents().find(\'#rscomm_subscribe\').click();">'.JText::_('COM_RSCOMMENTS_SUBSCRIBE').'</button><button type="button" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</button>';
	echo JHtml::_('bootstrap.renderModal', 'rscomments-subscribe', array('title' => JText::_('COM_RSCOMMENTS_SUBSCRIBE'), 'url' => $sURL, 'footer' => $footer, 'bodyHeight' => '70'));
}

if ($this->config->enable_reports && $this->config->modal == 1) {
	$footer = '<button class="btn btn-primary" type="button" onclick="jQuery(\'#rscomments-report iframe\').contents().find(\'#rscomm_report\').click();">'.JText::_('COM_RSCOMMENTS_REPORT').'</button><button type="button" data-dismiss="modal" class="btn">'.JText::_('COM_RSCOMMENTS_CLOSE').'</button>';
	echo JHtml::_('bootstrap.renderModal', 'rscomments-report', array('title' => JText::_('COM_RSCOMMENTS_REPORT'), 'url' => $rURL, 'footer' => $footer, 'bodyHeight' => '70'));
}

if ($this->config->modal == 1 && $this->config->enable_usercomments) {
	echo JHtml::_('bootstrap.renderModal', 'rscomments-mycomments', array('title' => JText::_('COM_RSCOMMENTS_MY_COMMENTS'), 'url' => $cURL, 'bodyHeight' => '70'));
}

echo '<input type="hidden" name="rscomments_comments" value="'.$cURL.'" />';
echo '<input type="hidden" name="rscomments_terms" value="'.$tURL.'" />';
echo '<input type="hidden" name="rscomments_subscribe" value="'.$sURL.'" />';
echo '<input type="hidden" name="rscomments_report" value="'.$rURL.'" />';

echo '<input type="hidden" name="rscomments_id" value="" />';
echo '<input type="hidden" name="rscomments_option" value="" />';
echo '<input type="hidden" name="rscomments_cid" value="" />'; ?>