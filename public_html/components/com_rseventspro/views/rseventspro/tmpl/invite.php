<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JText::script('ERROR');
JText::script('COM_RSEVENTSPRO_INVITE_FROM_ERROR');
JText::script('COM_RSEVENTSPRO_INVITE_FROM_NAME_ERROR');
JText::script('COM_RSEVENTSPRO_INVITE_EMAILS_ERROR');
JText::script('COM_RSEVENTSPRO_INVITE_CAPTCHA_ERROR');
$importBtns = ''; 
if (!empty($this->config->google_client_id)) $importBtns .= '<a class="btn btn-primary" href="javascript:void(0)" onclick="rs_google_auth();">'.JText::_('COM_RSEVENTSPRO_INVITE_FROM_GMAIL').'</a>';
if ($this->auth) $importBtns .= ' <a class="btn btn-primary" href="'.$this->auth.'">'.JText::_('COM_RSEVENTSPRO_INVITE_FROM_YAHOO').'</a>'; ?>

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

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" name="rseInviteForm" id="rseInviteForm">
	<h2><?php echo JText::sprintf('COM_RSEVENTSPRO_INVITE_FRIENDS',$this->event->name); ?></h2>
		
	<div class="form-horizontal rsepro-horizontal">
		<?php echo RSEventsproAdapterGrid::renderField('', $importBtns) ?>
		<?php echo $this->form->renderField('from'); ?>
		<?php echo $this->form->renderField('from_name'); ?>
	</div>
	
	<div class="form-vertical">
		<?php echo $this->form->renderField('emails'); ?>
		<?php if ($this->config->email_invite_message) echo $this->form->renderField('message'); ?>
	</div>
	
	<?php if (in_array(1,$this->captcha_use)) { ?>
	<div class="rsepro-invite-captcha">
		<?php if ($this->config->captcha == 1) { ?>			
		<img id="captcha" src="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=captcha&tmpl=component&rand='.rand(),false); ?>" onclick="javascript:reloadCaptcha()" />
		<span class="explain">
			<?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_TEXT'); ?> <br /> <?php echo JText::_('COM_RSEVENTSPRO_CAPTCHA_RELOAD'); ?>
		</span>
		<input type="text" id="secret" name="secret" value="" class="input-small" />
		<?php } elseif ($this->config->captcha == 2) { ?>
		<div id="rse-g-recaptcha"></div>
		<?php } elseif ($this->config->captcha == 3) { ?>
		<div id="h-captcha-rseInvite"></div>
		<?php } ?>
	</div>
	<?php } ?>
	
	<div class="form-actions">
		<button id="rseInviteBtn" type="submit" class="btn btn-primary" onclick="return rs_invite();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
		<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id))); ?>
	</div>
	
	<?php echo JHTML::_( 'form.token' )."\n"; ?>
	<input type="hidden" name="task" value="rseventspro.invite" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
</form>