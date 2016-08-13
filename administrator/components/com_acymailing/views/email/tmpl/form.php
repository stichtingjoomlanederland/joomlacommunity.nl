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
	<div id="iframedoc"></div>
	<form action="index.php?tmpl=component&amp;option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" enctype="multipart/form-data">
		<div id="iframetemplate"></div>
		<div id="iframetag"></div>

		<?php include(dirname(__FILE__).DS.'param.'.basename(__FILE__)); ?>
		<br/>

		<div class="onelineblockoptions"<?php if(empty($this->mail->html)) echo ' style="display:none;"'; ?>>
			<span class="acyblocktitle"><?php echo JText::_('HTML_VERSION'); ?></span>
			<?php echo $this->editor->display(); ?>
		</div>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo JText::_('TEXT_VERSION'); ?></span>
			<textarea style="width:98%;min-height:150px;" rows="20" name="data[mail][altbody]" id="altbody" placeholder="<?php echo JText::_('AUTO_GENERATED_HTML'); ?>"><?php echo @$this->mail->altbody; ?></textarea>
		</div>

		<div class="clr"></div>
		<input type="hidden" name="cid[]" value="<?php echo @$this->mail->mailid; ?>"/>
		<?php if(!empty($this->mail->type)){ ?>
			<input type="hidden" name="data[mail][type]" value="<?php echo $this->mail->type; ?>"/>
		<?php } ?>
		<input type="hidden" id="tempid" name="data[mail][tempid]" value="<?php echo @$this->mail->tempid; ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="email"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
