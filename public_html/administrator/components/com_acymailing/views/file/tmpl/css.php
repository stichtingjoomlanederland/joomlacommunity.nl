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
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
		<textarea style="width:98%;height:350px;" rows="20" name="csscontent"><?php echo $this->content; ?></textarea>

		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value="savecss"/>
		<input type="hidden" name="ctrl" value="file"/>
		<input type="hidden" name="tmpl" value="component"/>
		<input type="hidden" name="file" value="<?php echo $this->type.'_'.$this->fileName; ?>"/>
		<input type="hidden" name="var" value="<?php echo JRequest::getCmd('var'); ?>"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
