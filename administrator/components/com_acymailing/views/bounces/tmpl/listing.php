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
	<?php
	if(ACYMAILING_J30){
		$saveOrderingUrl = 'index.php?option=com_acymailing&task=saveorder&tmpl=component';
		JHtml::_('sortablelist.sortable', 'bounceListing', 'adminForm', 'asc', $saveOrderingUrl);
	}
	?>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>&amp;ctrl=bounces" autocomplete="off" method="post" name="adminForm" id="adminForm">
		<div class="acyblockoptions" style="float:none; display: block;">
			<span class="acyblocktitle"><?php echo JText::_('ACY_CONFIGURATION'); ?></span>
			<table style="width:100%;">
				<tr>
					<td>
						<table class="acymailing_table" cellspacing="1">
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_ADDRESS'); ?>
								</td>
								<td>
									<?php echo $this->config->get('bounce_email'); ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('SMTP_SERVER'); ?>
								</td>
								<td>
									<input type="text" style="width:200px" name="config[bounce_server]" value="<?php echo $this->escape(trim($this->config->get('bounce_server', ''))); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('SMTP_PORT'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_port]" value="<?php echo intval($this->config->get('bounce_port', '')); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_CONNECTION'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_connection; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('SMTP_SECURE'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_secured; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_CERTIF'); ?>
								</td>
								<td>
									<?php echo $this->elements->bounce_certif; ?>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('ACY_USERNAME'); ?>
								</td>
								<td>
									<input type="text" autocomplete="off" style="width:160px" name="config[bounce_username]" value="<?php echo $this->escape(trim($this->config->get('bounce_username', ''))); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('SMTP_PASSWORD'); ?>
								</td>
								<td>
									<input type="text" autocomplete="off" style="width:160px" name="config[bounce_password]" value="<?php echo str_repeat('*', strlen($this->config->get('bounce_password'))); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_TIMEOUT'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_timeout]" value="<?php echo intval($this->config->get('bounce_timeout', '10')); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_MAX_EMAIL'); ?>
								</td>
								<td>
									<input type="text" style="width:50px" name="config[bounce_max]" value="<?php echo intval($this->config->get('bounce_max', 100)); ?>"/>
								</td>
							</tr>
							<tr>
								<td class="acykey">
									<?php echo JText::_('BOUNCE_FEATURE'); ?>
								</td>
								<td>
									<?php echo JHTML::_('acyselect.booleanlist', "config[auto_bounce]", 'onclick="displayBounceFrequency(this.value);"', $this->config->get('auto_bounce', 0)); ?>
								</td>
							</tr>
						</table>
					</td>
					<td valign="bottom" style="padding-left: 50px;">
						<table id="bouncefrequency" class="acymailing_table" cellspacing="1">
							<tr>
								<td class="acykey"><?php echo JText::_('FREQUENCY'); ?></td>
								<td><?php $freqBounce = acymailing_get('type.delay');
									echo $freqBounce->display('config[auto_bounce_frequency]', $this->config->get('auto_bounce_frequency', 21600), 1); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo JText::_('LAST_RUN'); ?></td>
								<td><?php echo acymailing_getDate($this->config->get('auto_bounce_last')); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo JText::_('NEXT_RUN'); ?></td>
								<td><?php echo acymailing_getDate($this->config->get('auto_bounce_next')); ?></td>
							</tr>
							<tr>
								<td class="acykey"><?php echo JText::_('REPORT'); ?></td>
								<td><?php echo $this->config->get('auto_bounce_report'); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<?php
		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->htmlclass = 'bounce_menu';
		$acyToolbar->add();
		$acyToolbar->edit();
		$acyToolbar->delete();
		$acyToolbar->topfixed = false;
		$acyToolbar->display();
		?>
		<div class="acyblockoptions" style="display: block; float: none;">
			<span class="acyblocktitle"><?php echo JText::_('BOUNCE_RULES'); ?></span>
			<table class="acymailing_table" cellspacing="1" id="bounceListing">
				<thead>
				<tr>
					<th class="title titlenum">
						<?php echo JText::_('ACY_NUM'); ?>
					</th>
					<?php if(ACYMAILING_J30){ ?>
						<th class="title titleorder">
							<?php echo '<i class="icon-menu-2"></i>'; ?>
						</th>
					<?php } ?>
					<th class="title titlebox">
						<input type="checkbox" name="toggle" value="" onclick="acymailing_js.checkAll(this);"/>
					</th>
					<th class="title">
						<?php echo JText::_('ACY_NAME'); ?>
					</th>
					<th>
						<?php echo JText::_('BOUNCE_ACTION'); ?>
					</th>
					<th>
						<?php echo JText::_('EMAIL_ACTION'); ?>
					</th>
					<?php if(!ACYMAILING_J30){ ?>
						<th class="title titleorder">
							<?php echo JText::_('ACY_ORDERING'); ?>
							<?php echo JHTML::_('grid.order', $this->rows); ?>
						</th>
					<?php } ?>
					<th class="title titletoggle">
						<?php echo JText::_('ENABLED'); ?>
					</th>
					<th class="title titleid">
						<?php echo JText::_('ACY_ID'); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$k = 0;

				for($i = 0, $a = count($this->rows); $i < $a; $i++){
					$row =& $this->rows[$i];
					$publishedid = 'published_'.$row->ruleid;
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td align="center" style="text-align:center">
							<?php echo $i + 1; ?>
						</td>
						<?php if(ACYMAILING_J30){ ?>
							<td class="order" width="3%">
						<span class="sortable-handler">
							<i class="icon-menu"></i>
						</span>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order"/>
							</td>
						<?php } ?>
						<td align="center" style="text-align:center">
							<?php echo JHTML::_('grid.id', $i, $row->ruleid); ?>
						</td>
						<td>
							<?php
							echo '<a href="'.acymailing_completeLink('bounces&task=edit&ruleid='.$row->ruleid).'" >'.JText::_($row->name).'</a>'; ?>
						</td>
						<td>
							<?php if(isset($row->action_user['removesub'])) echo JText::_('REMOVE_SUB').'<br />';
							if(isset($row->action_user['unsub'])) echo JText::_('UNSUB_USER').'<br />';
							if(isset($row->action_user['sub'])) echo JText::_('SUBSCRIBE_USER').' ( '.$this->lists[$row->action_user['subscribeto']]->name.' )<br />';
							if(isset($row->action_user['block'])) echo JText::_('BLOCK_USER').'<br />';
							if(isset($row->action_user['delete'])) echo JText::_('DELETE_USER');
							?>
						</td>
						<td>
							<?php if(isset($row->action_message['save'])) echo JText::_('BOUNCE_SAVE_MESSAGE').'<br />';
							if(isset($row->action_message['delete'])) echo JText::_('DELETE_EMAIL').'<br />';
							if(!empty($row->action_message['forwardto'])) echo JText::_('FORWARD_EMAIL').' '.$row->action_message['forwardto'];

							?>
						</td>
						<?php if(!ACYMAILING_J30){ ?>
							<td class="order">
								<span><?php echo $this->pagination->orderUpIcon($i, $row->ordering >= @$this->rows[$i - 1]->ordering, 'orderup', 'Move Up', true); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $a, $row->ordering <= @$this->rows[$i + 1]->ordering, 'orderdown', 'Move Down', true); ?></span>
								<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center"/>
							</td>
						<?php } ?>
						<td align="center" style="text-align:center">
							<span id="<?php echo $publishedid ?>" class="loading"><?php echo $this->toggleClass->toggle($publishedid, (int)$row->published, 'rules') ?></span>
						</td>
						<td align="center" style="text-align:center">
							<?php echo $row->ruleid; ?>
						</td>

					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
				</tbody>
			</table>
		</div>

		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="bounces"/>
		<input type="hidden" name="boxchecked" value="0"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
