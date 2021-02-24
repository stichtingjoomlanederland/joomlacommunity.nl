<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=comment&layout=edit&IdComment='.(int) $this->item->IdComment); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-horizontal">
	<?php 
		$title = RSCommentsHelperAdmin::ArticleTitle($this->item->option, $this->item->id);
		
		foreach ($this->form->getFieldset() as $field) { 
			if ($field->id == 'jform_date') continue;
			
			if ($field->id == 'jform_ip') {
				if ($this->item->location) {
					$locationlink = $this->item->coordinates ? 'https://www.google.com/maps/place/'.$this->item->coordinates : 'javascript: void(0)';
					echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_LOCATION'), '<a href="'.$locationlink.'" target="_blank" class="btn btn-info btn-small btn-sm"><i class="icon-location"></i> '.$this->item->location.'</a>');
				}
				
				echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_DATE'), RSCommentsHelperAdmin::showDate($this->item->date), true);
				echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_IP'), '<a href="https://apps.db.ripe.net/search/query.html?searchtext='.$this->item->ip.'" class="btn btn-info btn-small btn-sm" target="_blank">'.$this->item->ip.'</a>');
				echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT'), RSCommentsHelperAdmin::component($this->item->option).($title ? ' - <em>'.$title.'</em>' : '').' &mdash; ID: '.$this->item->id, true);
				
				if (!empty($this->item->url)) {
					echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW'), '<a href="'.JURI::root().base64_decode($this->item->url).'" target="_blank" class="btn btn-info btn-small btn-sm"><i class="icon-eye-open"></i> '.JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW_ITEM').'</a>');
				}
				
				if (!empty($this->item->file)) {
					echo RSCommentsAdapterGrid::renderField(JText::_('COM_RSCOMMENTS_COMMENT_DOWNLOAD'), '<a href="'.JRoute::_('index.php?option=com_rscomments&task=download&id='.$this->item->IdComment).'" target="_blank" class="btn btn-info btn-small btn-sm"><i class="icon-download"></i> '.$this->item->file.'</a>');
				}
				
			} else {
				echo $field->renderField();
			}
		}
	?>

	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" value="" name="task" />
</form>