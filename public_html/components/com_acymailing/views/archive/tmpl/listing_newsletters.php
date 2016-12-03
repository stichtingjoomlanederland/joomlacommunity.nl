<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if($this->values->filter){ ?>
	<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $this->escape($this->pageInfo->search); ?>" class="inputbox"/>
	<button class="btn button buttongo" onclick="this.form.submit();"><?php echo JText::_('JOOMEXT_GO'); ?></button>
	<button class="btn button buttonreset" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo JText::_('JOOMEXT_RESET'); ?></button>
<?php }
echo $this->ordering;
$k = 1;
for($i = 0, $a = count($this->rows); $i < $a; $i++){
	$row =& $this->rows[$i];
	echo '<div class="archiveRow archiveRow'.$k.$this->values->suffix.'">';

	if(!empty($row->thumb)) echo '<img class="archiveItemPict" src="'.$row->thumb.'"/>';
	echo '<span class="acyarchivetitle"><a '.($this->config->get('open_popup', 1) ? 'class="modal" rel="{handler: \'iframe\', size: {x: '.intval($this->config->get('popup_width', 750)).', y: '.intval($this->config->get('popup_height', 550)).'}}"' : '').'href="'.acymailing_completeLink('archive&task=view&listid='.$row->listid.'&mailid='.$row->mailid.'-'.strip_tags($row->alias).$this->item, (bool)$this->config->get('open_popup', 1)).'">';
	echo acymailing_dispSearch($row->subject, $this->pageInfo->search).'</a>';
	if(in_array($row->listid, $this->manageableLists)){
		if(($this->config->get('frontend_modif', 1) || ($row->userid == $this->my->id)) && ($this->config->get('frontend_modif_sent', 1) || empty($row->senddate))){ ?>
			<span class="acyeditbutton"><a href="<?php echo acymailing_completeLink('frontnewsletter&task=edit&mailid='.$row->mailid.'&listid='.$row->listid); ?>" title="<?php echo JText::_('ACY_EDIT', true) ?>"><img class="icon16" src="<?php echo ACYMAILING_IMAGES ?>icons/icon-16-edit.png" alt="<?php echo JText::_('ACY_EDIT', true) ?>"/></a></span>
		<?php }

		if(!empty($row->senddate) && acymailing_isAllowed($this->config->get('acl_statistics_manage', 'all'))){ ?>
			<span class="acystatsbutton"><a class="modal" rel="{handler: 'iframe', size: {x: 800, y: 590}}" href="<?php echo acymailing_completeLink('frontnewsletter&task=stats&mailid='.$row->mailid.'&listid='.$row->listid, true); ?>"><img src="<?php echo ACYMAILING_IMAGES; ?>icons/icon-16-stats.png" alt="<?php echo JText::_('STATISTICS', true) ?>"/></a></span>
		<?php } ?>
	<?php }
	echo '</span>';
	if($this->values->show_senddate && !empty($row->senddate)){
		echo '<span class="sentondate">'.JText::sprintf('ACY_SENT_ON', acymailing_getDate($row->senddate, JText::_('DATE_FORMAT_LC3'))).'</span>';
	}
	if($this->values->show_receiveemail){ ?>
		<span class="receiveviaemail">
				<input onclick="changeReceiveEmail(this.checked)" type="checkbox" name="receivemail[]" value="<?php echo $row->mailid; ?>" id="receive_<?php echo $row->mailid; ?>"/> <label for="receive_<?php echo $row->mailid; ?>"><?php echo JText::_('RECEIVE_VIA_EMAIL'); ?></label>
			</span>
		<?php
		if(!empty($row->summary)) echo '<br/>';
	}
	if(!empty($row->summary)) echo '<span class="archiveItemDesc">'.nl2br($row->summary).'</span>';
	echo '</div>';
	$k = 3 - $k;
}
?>
<div class="archivePagination">
	<div class="sectiontablefooter<?php echo $this->values->suffix; ?> pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
	<div class="sectiontablefooter<?php echo $this->values->suffix; ?>"><?php echo $this->pagination->getPagesCounter(); ?></div>
</div>
