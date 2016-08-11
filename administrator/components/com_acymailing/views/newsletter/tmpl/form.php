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
	<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<input type="hidden" name="cid[]" value="<?php echo @$this->mail->mailid; ?>"/>
		<input type="hidden" id="tempid" name="data[mail][tempid]" value="<?php echo @$this->mail->tempid; ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<?php $type = empty($this->mail->type) ? 'news' : $this->mail->type; ?>
		<input type="hidden" name="data[mail][type]" value="<?php echo $type; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<?php echo JHTML::_('form.token'); ?>
		<div style="clear: both;">
			<div id="newsletterLeftColumn">
				<div class="acyblockoptions acyblock_newsletter">
					<span class="acyblocktitle"><?php echo JText::_('ACY_NEWSLETTER_INFORMATION'); ?></span>
					<?php include(dirname(__FILE__).DS.'info.'.basename(__FILE__)); ?>
				</div>
				<div class="acyblockoptions acyblock_newsletter" id="htmlfieldset">
					<span class="acyblocktitle"> <?php echo JText::_('HTML_VERSION'); ?></span>
					<?php echo $this->editor->display(); ?>
				</div>
				<div class="acyblockoptions acyblock_newsletter" id="textfieldset">
					<span class="acyblocktitle"> <?php echo JText::_('TEXT_VERSION'); ?></span>
					<textarea style="width:98%;min-height:250px;" rows="20" name="data[mail][altbody]" id="altbody" placeholder="<?php echo JText::_('AUTO_GENERATED_HTML'); ?>" onClick="zoneToTag='altbody';"><?php echo $this->escape(@$this->mail->altbody); ?></textarea>
				</div>
			</div>
			<div id="newsletterRightColumn" class="acyblockoptions">
				<?php include(dirname(__FILE__).DS.'param.'.basename(__FILE__)); ?>
			</div>
		</div>
		<div class="clr"></div>
	</form>
</div>
