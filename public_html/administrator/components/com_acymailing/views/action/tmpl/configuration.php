<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo JText::_('ACY_CONFIGURATION'); ?></span>
	<table cellspacing="1" width="100%">
		<tr>
			<td class="acykey">
				<label for="smtpserver">
					<?php echo JText::_('SMTP_SERVER'); ?>
				</label>
			</td>
			<td>
				<input type="text" name="data[action][server]" id="smtpserver" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->action->server); ?>"/>
			</td>
			<td class="acykey">
				<label for="connection_method">
					<?php echo JText::_('BOUNCE_CONNECTION'); ?>
				</label>
			</td>
			<td>
				<?php
				$connections = array('imap' => 'IMAP', 'pop3' => 'POP3', 'pear' => 'POP3 (without imap extension)', 'nntp' => 'NNTP');

				$connecvals = array();
				foreach($connections as $code => $string){
					$connecvals[] = JHTML::_('select.option', $code, $string);
				}

				echo JHTML::_('select.genericlist', $connecvals, 'data[action][connection_method]', 'size="1"', 'value', 'text', $this->escape(@$this->action->connection_method));
				?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<label for="smtpusername">
					<?php echo JText::_('ACY_USERNAME'); ?>
				</label>
			</td>
			<td>
				<input type="text" name="data[action][username]" id="smtpusername" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->action->username); ?>"/>
			</td>
			<td class="acykey">
				<label for="secure_method">
					<?php echo JText::_('SMTP_SECURE'); ?>
				</label>
			</td>
			<td>
				<?php
				$securedVals = array();
				$securedVals[] = JHTML::_('select.option', '', '- - -');
				$securedVals[] = JHTML::_('select.option', 'ssl', 'SSL');
				$securedVals[] = JHTML::_('select.option', 'tls', 'TLS');

				echo JHTML::_('select.genericlist', $securedVals, "data[action][secure_method]", 'size="1"', 'value', 'text', $this->escape(@$this->action->secure_method));
				?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<label for="password">
					<?php echo JText::_('SMTP_PASSWORD'); ?>
				</label>
			</td>
			<td>
				<?php $password = empty($this->action->password) ? '' : '********'; ?>
				<input type="text" name="data[action][password]" id="password" class="inputbox" style="width:200px" value="<?php echo $password; ?>"/>
			</td>
			<td class="acykey">
				<label for="port">
					<?php echo JText::_('SMTP_PORT'); ?>
				</label>
			</td>
			<td>
				<input type="text" name="data[action][port]" id="port" class="inputbox" style="width:50px" value="<?php echo $this->escape(@$this->action->port); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<label for="self_signed">
					<?php echo JText::_('BOUNCE_CERTIF'); ?>
				</label>
			</td>
			<td colsapn="3">
				<?php echo JHTML::_('acyselect.booleanlist', "data[action][self_signed]", '', @$this->action->self_signed); ?>
			</td>
		</tr>
	</table>
</div>
