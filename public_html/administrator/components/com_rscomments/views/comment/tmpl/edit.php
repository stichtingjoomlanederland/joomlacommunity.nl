<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=comment&layout=edit&IdComment='.(int) $this->item->IdComment); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
<?php
	echo JHtml::_('rsfieldset.start', 'adminform', '');
	foreach ($this->form->getFieldset() as $field) {
		if ($field->name == 'jform[date]') continue;
		
		if($field->name == 'jform[ip]') {
			
			if ($this->item->location) {
				$locationlink = $this->item->coordinates ? 'https://www.google.com/maps/place/'.$this->item->coordinates : 'javascript: void(0)';
				echo JHtml::_('rsfieldset.element', '<label id="jform_location-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_LOCATION_DESC')).'" for="jform_location">'.JText::_('COM_RSCOMMENTS_COMMENT_LOCATION').'</label>', '<a href="'.$locationlink.'" target="_blank" class="btn btn-info btn-small fltlft"><i class="icon-location"></i> '.$this->item->location.'</a>');
			}
			
			echo JHtml::_('rsfieldset.element', '<label id="jform_date-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_DATE_DESC')).'" for="jform_ip">'.JText::_('COM_RSCOMMENTS_COMMENT_DATE').'</label>', '<button class="btn btn-info btn-small fltlft" type="button"><i class="icon-clock"></i> '.RSCommentsHelper::showDate($this->item->date).'</button>');
			
			echo JHtml::_('rsfieldset.element', '<label id="jform_ip-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_IP_DESC')).'" for="jform_ip">'.JText::_('COM_RSCOMMENTS_COMMENT_IP').'</label>', '<a href="https://apps.db.ripe.net/search/query.html?searchtext='.$this->item->ip.'" id="jform_ip" class="btn btn-info btn-small fltlft" target="_blank"><i class="icon-question-sign"></i> '.$this->item->ip.'</a>');
			
			echo JHtml::_('rsfieldset.element', '<label id="jform_ip-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT_DESC')).'" for="jform_ip">'.JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT').'</label>', '<span class="fltlft" style="margin-top:5px;">'.RSCommentsHelper::component($this->item->option).'</span>');
			
			if (!empty($this->item->url)) {
				echo JHtml::_('rsfieldset.element', '<label id="jform_previewlink-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW_DESC')).'" for="jform_previewlink">'.JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW').'</label>', '<a href="'.JURI::root().base64_decode($this->item->url).'" target="_blank" id="jform_previewlink" class="btn btn-info btn-small fltlft"><i class="icon-eye-open"></i> '.JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW_ITEM').'</a>');
			}
			
			if (!empty($this->item->file)) {
				echo JHtml::_('rsfieldset.element', '<label id="jform_file-lbl" class="'.RSTooltip::tooltipClass().'" title="'.RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_DOWNLOAD_DESC')).'" for="jform_file">'.JText::_('COM_RSCOMMENTS_COMMENT_DOWNLOAD').'</label>', '<a href="'.JRoute::_('index.php?option=com_rscomments&task=download&id='.$this->item->IdComment).'" target="_blank" id="jform_file" class="btn btn-info btn-small fltlft"><i class="icon-download"></i> '.$this->item->file.'</a>');
			}

		} else {
			echo JHtml::_('rsfieldset.element', $field->label, $field->input);
		}
	}
	echo JHtml::_('rsfieldset.end');
?>

<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" value="" name="task" />
</form>