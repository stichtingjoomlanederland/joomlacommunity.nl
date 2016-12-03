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
	<form id="adminForm" name="adminForm" method="post" action="index.php">
		<div class="mail-information">
			<div class="acyblockoptions mail-part">
				<span><?php echo JText::_('SIMPLE_SENDING_INTRO') ?></span>
			</div>
			<div class="acyblockoptions mail-part">
				<span class="acyblocktitle"><?php echo JText::_('SIMPLE_SENDING_RECEIVERS') ?></span>
				<?php echo $this->testreceiverType->display($this->infos->test_selection, $this->infos->test_group, $this->infos->test_emails); ?>
			</div>
			<div class="acyblockoptions mail-part acyblock_newsletter" id="htmlfieldset">
				<span class="acyblocktitle"><?php echo JText::_('SIMPLE_SENDING_CONTENT') ?></span>
				<input type="text" name="subject" id="subject" placeholder="<?php echo JText::_('JOOMEXT_SUBJECT') ?>" value="<?php echo $this->subject ?>">
				<?php echo $this->editor->display(); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="send"/>
		<input type="hidden" name="ctrl" value="simplemail"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" id="tempid" name="tempid" value="<?php echo $this->tempid ?>"/>
	</form>
</div>
