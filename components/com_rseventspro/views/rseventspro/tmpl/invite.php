<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<script type="text/javascript">
var invitemessage = new Array();
invitemessage[0] = '<?php echo JText::_('COM_RSEVENTSPRO_INVITE_USERNAME_PASSWORD_ERROR',true); ?>';
</script>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" name="adminForm" id="adminForm" onsubmit="checkcaptcha();">
	<h3><?php echo JText::sprintf('COM_RSEVENTSPRO_INVITE_FRIENDS',$this->event->name); ?></h3>

	<a class="rs_invite_btn" href="javascript:void(0)" onclick="rs_load('gmail')"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_GMAIL'); ?></a> 
	<a class="rs_invite_btn" href="javascript:void(0)" onclick="rs_load('yahoo')"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_YAHOO'); ?></a>
	<div class="rs_clear"></div>
	<br />
	
	<table class="rs_table" width="100%" cellspacing="0" cellpadding="3">
		<tr>
			<td width="100"><label for="from"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM'); ?></label></td>
			<td><input type="text" name="jform[from]" id="from" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr>
			<td width="100"><label for="from_name"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_NAME'); ?></label></td>
			<td><input type="text" name="jform[from_name]" id="from_name" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr id="rs_gmail_u" style="display:none;">
			<td><label for="gusername"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_USERNAME'); ?></label></td>
			<td><input type="text" id="gusername" name="gusername" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr id="rs_gmail_p" style="display:none;">
			<td><label for="gpassword"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_PASSWORD'); ?></label></td>
			<td><input type="password" id="gpassword" name="gpassword" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr id="rs_gmail_b" style="display:none;">
			<td>&nbsp;</td>
			<td>
				<button type="button" class="rs_invite_btn" onclick="rs_connect('gmail')"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_GCONNECT'); ?></button>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> <a href="javascript:void(0)" onclick="rs_load_close('gmail')"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
				<img id="img_gmail" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loading.gif" alt="" style="vertical-align:middle; display:none;" />
			</td>
		</tr>
		<tr id="rs_yahoo_u" style="display:none;">
			<td><label for="yusername"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_USERNAME'); ?></label></td> 
			<td><input type="text" id="yusername" name="yusername" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr id="rs_yahoo_p" style="display:none;">
			<td><label for="ypassword"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_PASSWORD'); ?></label></td> 
			<td><input type="password" id="ypassword" name="ypassword" value="" style="width: 65%;" class="rs_edit_inp_small" /></td>
		</tr>
		<tr id="rs_yahoo_b" style="display:none;">
			<td>&nbsp;</td>
			<td>
				<button type="button" class="rs_invite_btn" onclick="rs_connect('yahoo')"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_YCONNECT'); ?></button>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> <a href="javascript:void(0)" onclick="rs_load_close('yahoo')"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
				<img id="img_yahoo" src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loading.gif" alt="" style="vertical-align:middle; display:none;" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php echo JText::_('COM_RSEVENTSPRO_INVITE_INFO_EMAILS'); ?> <br />
				<textarea name="jform[emails]" id="emails" cols="60" rows="10" style="width: 70%;" class="rs_edit_txt"></textarea>
			</td>
		</tr>
		<?php if (rseventsproHelper::getConfig('email_invite_message','int')) { ?>
		<tr>
			<td colspan="2">
				<?php echo JText::_('COM_RSEVENTSPRO_INVITE_MESSAGE'); ?> <br />
				<textarea name="message" id="message" cols="60" rows="5" style="width: 70%;" class="rs_edit_txt"></textarea>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td>
				<img id="captcha" src="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=captcha&tmpl=component'); ?>" onclick="javascript:reloadCaptcha()" />
			</td>
			<td>
				<span class="explain">
					<?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_TEXT'); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_RELOAD'); ?>
				</span>
				<input type="text" id="secret" name="secret" value="" class="rs_edit_inp_small" />
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<button type="button" class="button btn btn-primary" onclick="checkcaptcha();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
				<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name))); ?>
			</td>
		</tr>
	</table>
	
	<?php echo JHTML::_( 'form.token' )."\n"; ?>
	<input type="hidden" name="task" value="rseventspro.invite" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
</form>