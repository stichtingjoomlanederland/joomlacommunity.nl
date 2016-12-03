<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!function_exists('ldap_connect')){
	acymailing_display('LDAP Extension not loaded on your server.<br />Please enable the LDAP php extension.', 'warning');
	return;
}

$js = "function updateldap(){
					document.getElementById('ldap_fields').innerHTML = '<span class=\"onload\"></span>';
					queryString = 'index.php?option=com_acymailing&tmpl=component&ctrl=data&task=ajaxload&importfrom=ldap';
					queryString += '&ldap_host='+document.getElementById('ldap_host').value;
					queryString += '&ldap_port='+document.getElementById('ldap_port').value;
					queryString += '&ldap_basedn='+document.getElementById('ldap_basedn').value;
					queryString += '&ldap_username='+document.getElementById('ldap_username').value;
					queryString += '&ldap_password='+document.getElementById('ldap_password').value;
					try{
						new Ajax(queryString,{ method: 'post', update: document.getElementById('ldap_fields')}).request();
					}catch(err){
						new Request({
						method: 'post',
						url: queryString,
						onSuccess: function(responseText, responseXML) {
							document.getElementById('ldap_fields').innerHTML = responseText;
						}
						}).send();
					}
				}";
$doc = JFactory::getDocument();
$doc->addScriptDeclaration($js);
?>

<span class="acyblocktitle"><?php echo JText::_('ACY_CONFIGURATION'); ?></span>
<table <?php echo $this->isAdmin ? 'class="acymailing_table"' : 'class="admintable table" cellspacing="1"' ?>>
	<?php if($this->config->get('require_confirmation')){ ?>
		<tr>
			<td class="acykey">
				<?php echo JText::_('IMPORT_CONFIRMED'); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "ldap_import_confirm", '', $this->config->get('ldap_import_confirm', 1), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<td class="acykey">
			<?php echo JText::_('GENERATE_NAME'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "ldap_generatename", '', $this->config->get('ldap_generatename', 1), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<?php echo JText::_('OVERWRITE_EXISTING'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "ldap_overwriteexisting", '', $this->config->get('ldap_overwriteexisting', 0), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<?php echo 'Delete AcyMailing user if it does not exists in LDAP'; ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "ldap_deletenotexists", '', $this->config->get('ldap_deletenotexists', 0), JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
</table>

<span class="acyblocktitle" style="margin-top: 20px;">Server</span>
<table <?php echo $this->isAdmin ? 'class="acymailing_table"' : 'class="admintable table" cellspacing="1"' ?>>
	<tr>
		<td class="acykey">
			<label for="ldap_host">Host</label>
		</td>
		<td>
			<input onchange="updateldap();" type="text" style="width:160px" name="ldap_host" id="ldap_host" value="<?php echo $this->escape($this->config->get('ldap_host')); ?>"/>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="ldap_port">Port</label>
		</td>
		<td>
			<input onchange="updateldap();" type="text" style="width:50px" name="ldap_port" id="ldap_port" value="<?php echo $this->escape($this->config->get('ldap_port')); ?>"/>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="ldap_username">RDN</label>
		</td>
		<td>
			<input onchange="updateldap();" type="text" style="width:160px" name="ldap_username" id="ldap_username" value="<?php echo $this->escape($this->config->get('ldap_username')); ?>"/>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="ldap_password"><?php echo JText::_('SMTP_PASSWORD'); ?></label>
		</td>
		<td>
			<input onchange="updateldap();" type="password" style="width:160px" name="ldap_password" id="ldap_password" value="<?php echo $this->escape($this->config->get('ldap_password')); ?>"/>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="ldap_basedn">Base DN</label>
		</td>
		<td>
			<input onchange="updateldap();" type="text" style="width:200px" name="ldap_basedn" id="ldap_basedn" value="<?php echo $this->escape($this->config->get('ldap_basedn')); ?>"/>
		</td>
	</tr>
</table>
