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
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<div>
			<?php if(!empty($this->lists)){ ?>
				<div class="onelineblockoptions">
					<span class="acyblocktitle"><?php echo JText::_('NEWSLETTER_SENT_TO'); ?></span>
					<table class="adminlist table table-striped" cellspacing="1" align="center">
						<tbody>
						<?php
						$k = 0;
						foreach($this->lists as $row){
							?>
							<tr class="<?php echo "row$k"; ?>">
								<td>
									<?php
									echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
									echo ' ( '.$row->nbsub.' )';
									?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
						} ?>
						</tbody>
					</table>
					<?php
					if(!empty($this->mail->filter)){
						$filterClass = acymailing_get('class.filter');
						$resultFilters = $filterClass->displayFilters($this->mail->filter);
						if(!empty($resultFilters)){
							echo '<br />'.JText::_('RECEIVER_LISTS').'<br />'.JText::_('FILTER_ONLY_IF');
							echo '<ul><li>'.implode('</li><li>', $resultFilters).'</li></ul>';
						}
					} ?>
				</div>
				<table class="acymailing_table">
					<tr>
						<td class="acykey">
							<?php echo JText::_('SEND_DATE'); ?>
						</td>
						<td>
							<?php echo JHTML::_('calendar', acymailing_getDate(time(), '%Y-%m-%d %H:%M'), 'senddate', 'senddate', '%Y-%m-%d %H:%M', array('style' => 'width:200px')); ?>
						</td>
					</tr>
					<tr>
						<td>
						</td>
						<td>
							<button class="btn btn-primary" type="submit"><?php echo JText::_('SCHEDULE'); ?></button>
						</td>
					</tr>
				</table>

			<?php }else{
				echo acymailing_display(JText::_('EMAIL_AFFECT'), 'warning');
			} ?>

		</div>
		<div class="clr"></div>
		<input type="hidden" name="mailid" value="<?php echo $this->mail->mailid; ?>"/>
		<input type="hidden" name="listid" value="<?php echo JRequest::getInt('listid'); ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value="schedule"/>
		<input type="hidden" name="ctrl" value="frontnewsletter"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
