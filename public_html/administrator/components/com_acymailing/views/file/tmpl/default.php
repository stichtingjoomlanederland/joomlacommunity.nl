<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<form action="index.php?tmpl=component&amp;option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
		<div class="onelineblockoptions">
			<div class="acyblocktitle"><?php echo JText::_('ACY_FILE').' : '.@$this->escape($this->file->name); ?>
				<?php if(!empty($this->showLatest)){ ?>
					<button type="button" class="acymailing_button" onclick="javascript:submitbutton('latest')" style="margin-left: 15px !important;"> <?php echo JText::_('LOAD_LATEST_LANGUAGE'); ?> <i class="acyicon-import" style="margin-left: 10px;"></i></button>
				<?php } ?>
			</div>
			<textarea style="width:660px;height:200px;" rows="18" name="content" id="translation"><?php echo @$this->file->content; ?></textarea>
		</div>

		<div class="onelineblockoptions">
			<div class="acyblocktitle"><?php echo JText::_('CUSTOM_TRANS'); ?></div>
			<?php echo JText::_('CUSTOM_TRANS_DESC'); ?>
			<textarea style="width:660px;height:50px;" rows="5" name="customcontent"><?php echo @$this->file->customcontent; ?></textarea>
		</div>

		<div class="clr"></div>
		<input type="hidden" name="code" value="<?php echo @$this->escape($this->file->name); ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="file"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
