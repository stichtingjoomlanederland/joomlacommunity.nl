<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
var invitemessage = new Array();
invitemessage[0] = '<?php echo JText::_('COM_RSEVENTSPRO_INVITE_USERNAME_PASSWORD_ERROR',true); ?>';

<?php if (!empty($this->config->google_client_id)) { ?>
function rs_google_auth() {
	var config = {
		'client_id': '<?php echo addslashes($this->config->google_client_id); ?>',
		'scope': 'https://www.google.com/m8/feeds'
	};
	
	gapi.auth.authorize(config, function() {
		rs_google_contacts(gapi.auth.getToken());
	});
}
<?php } ?>
</script>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" name="adminForm" id="adminForm" onsubmit="checkcaptcha();">
	<h3><?php echo JText::sprintf('COM_RSEVENTSPRO_INVITE_FRIENDS',$this->event->name); ?></h3>

	<?php if (!empty($this->config->google_client_id)) { ?><a class="rs_invite_btn" href="javascript:void(0)" onclick="rs_google_auth();"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_GMAIL'); ?></a><?php } ?> 
	<?php if ($this->auth) { ?><a class="rs_invite_btn" href="<?php echo $this->auth; ?>"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_YAHOO'); ?></a><?php } ?>
	<div class="rs_clear"></div>
	<br />
	
	<div class="form-horizontal">
		<div class="control-group">
			<div class="control-label">
				<label for="jform_from"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM'); ?></label>
			</div>
			<div class="controls">
				<input type="text" name="jform[from]" id="jform_from" value="" class="input-xlarge" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<label for="jform_from_name"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_FROM_NAME'); ?></label>
			</div>
			<div class="controls">
				<input type="text" name="jform[from_name]" id="jform_from_name" value="" class="input-xlarge" />
			</div>
		</div>
	</div>
	
	<div class="form-vertical">
		<div class="control-group">
			<div class="control-label">
				<label for="emails"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_INFO_EMAILS'); ?></label>
			</div>
			<div class="controls">
				<textarea name="jform[emails]" id="emails" cols="60" rows="10" class="input-xxlarge"><?php echo $this->contacts; ?></textarea>
			</div>
		</div>
		
		<?php if (rseventsproHelper::getConfig('email_invite_message','int')) { ?>
		<div class="control-group">
			<div class="control-label">
				<label for="message"><?php echo JText::_('COM_RSEVENTSPRO_INVITE_MESSAGE'); ?></label>
			</div>
			<div class="controls">
				<textarea name="message" id="message" cols="60" rows="5" class="input-xxlarge"></textarea>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<div class="form-horizontal">
		<div class="control-group">
			<?php if ($this->config->captcha == 2) { ?>
			<div id="rse-g-recaptcha"></div>
			<?php } else { ?>
			<div class="control-label">
				<img id="captcha" src="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=captcha&tmpl=component'); ?>" onclick="javascript:reloadCaptcha()" />
			</div>
			<div class="controls">
				<span class="explain">
					<?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_TEXT'); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_RELOAD'); ?>
				</span>
				<input type="text" id="secret" name="secret" value="" class="input-small" />
			</div>
			<?php } ?>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="button" class="button btn btn-primary" onclick="checkcaptcha();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
		<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id))); ?>
	</div>
	
	<?php echo JHTML::_( 'form.token' )."\n"; ?>
	<input type="hidden" name="task" value="rseventspro.invite" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
</form>