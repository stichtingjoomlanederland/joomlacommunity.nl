<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset>
	<div class="acyheader icon-48-stats" style="float: left;"><?php echo $this->mailing->subject; ?></div>
	<div class="toolbar" id="toolbar" style="float: right;">
		<table>
			<tr>
				<?php
				$config = acymailing_config();
				if(acymailing_isAllowed($config->get('acl_subscriber_export', 'all'))){
					if(ACYMAILING_J16 && !$app->isAdmin()){
						$exportAction = "Joomla.submitbutton('export'); return false;";
					}else $exportAction = "javascript:submitbutton('export')";
					?>
					<td><a onclick="<?php echo $exportAction; ?>" href="#"><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT', true); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td>
				<?php } ?>
				<td><a href="<?php $link = 'frontnewsletter&task=stats&listid='.JRequest::getInt('listid');
					echo acymailing_completeLink($link.'&mailid='.JRequest::getInt('filter_mail'), true); ?>"><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL', true); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
			</tr>
		</table>
	</div>
</fieldset>
