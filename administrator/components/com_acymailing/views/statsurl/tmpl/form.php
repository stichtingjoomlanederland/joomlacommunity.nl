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
		<table class="adminform" cellspacing="1" width="100%">
			<tr>
				<td>
					<label for="name">
						<?php echo JText::_('URL_NAME'); ?>
					</label>
				</td>
				<td>
					<input type="text" name="data[url][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->url->name); ?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="url">
						<?php echo JText::_('URL'); ?>
					</label>
				</td>
				<td>
					<input type="text" name="data[url][url]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->url->url); ?>"/>
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="cid[]" value="<?php echo $this->url->urlid; ?>"/>
		<input type="hidden" name="ctrl" value="statsurl"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
