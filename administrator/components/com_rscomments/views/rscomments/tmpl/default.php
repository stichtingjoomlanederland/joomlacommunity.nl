<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.modal'); ?>

<?php if (!empty($this->stats)) { ?>
<script type="text/javascript">
	google.load('visualization', '1', {packages: ['corechart']});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable(
			<?php echo json_encode($this->stats); ?>
		);

		var options = { vAxis: {title: '<?php echo JText::_('COM_RSCOMMENTS_CHART_COMMENTS',true); ?>', 'format' : '0'}, legend: { position: 'none' } };
		var chart = new google.visualization.ColumnChart(document.getElementById('rscomments-chart'));
		chart.draw(data, options);
	}
	
	function rscomments_chart(value) {
		jQuery.ajax({
			url: 'index.php?option=com_rscomments',
			type: 'post',
			dataType : 'html',
			data: 'task=stats&type=' + value,
			success: function(response) {
				var data = google.visualization.arrayToDataTable(jQuery.parseJSON(response));
				var options = { vAxis: {title: '<?php echo JText::_('COM_RSCOMMENTS_CHART_COMMENTS',true); ?>', 'format' : '0'}, legend: { position: 'none' } };
				var chart = new google.visualization.ColumnChart(document.getElementById('rscomments-chart'));
				chart.draw(data, options);
			}
		});
	}
</script>
<?php } ?>

<div class="row-fluid">
	<div class="rsspan3 span4">
		<div class="fltlft">
			<div class="dashboard-container">
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
						<a href="index.php?option=com_rscomments&amp;view=comments">
							<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/comment.png', JText::_('COM_RSCOMMENTS_COMMENTS')); ?>
							<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_COMMENTS'); ?></span>
						</a>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
						<a href="index.php?option=com_rscomments&amp;view=emoticons">
							<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/emoticons.png', JText::_('COM_RSCOMMENTS_EMOTICONS')); ?>
							<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_EMOTICONS'); ?></span>
						</a>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_rscomments&amp;view=subscriptions">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/subscriptions.png', JText::_('COM_RSCOMMENTS_SUBSCRIPTIONS')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_SUBSCRIPTIONS'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_rscomments&amp;view=groups">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/groups.png', JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_rscomments&amp;view=import">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/import.png', JText::_('COM_RSCOMMENTS_IMPORT')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_IMPORT'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_rscomments&amp;view=messages">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/messages.png', JText::_('COM_RSCOMMENTS_MESSAGES')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_MESSAGES'); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
							<?php if(RSCommentsHelper::isJ3()) { ?>
								<a href="index.php?option=com_config&view=component&component=com_rscomments&return=<?php echo base64_encode(JURI::getInstance()); ?>">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/configuration.png', JText::_('COM_RSCOMMENTS_CONFIGURATION')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_CONFIGURATION'); ?></span>
								</a>
							<?php } else { ?>
								<a rel="{handler: 'iframe', size: {x: 875, y: 550}, onClose: function() {}}" href="index.php?option=com_config&view=component&component=com_rscomments&tmpl=component" class="modal">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/configuration.png', JText::_('COM_RSCOMMENTS_CONFIGURATION')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_CONFIGURATION'); ?></span>
								</a>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="rsspan4">
					<div class="dashboard-wraper">
						<div class="dashboard-content"> 
							<div class="dashboard-content">
								<a href="index.php?option=com_rscomments&amp;view=updates">
									<?php echo JHTML::_('image', 'administrator/components/com_rscomments/assets/images/updates.png', JText::_('COM_RSCOMMENTS_UPDATES')); ?>
									<span class="dashboard-title"><?php echo JText::_('COM_RSCOMMENTS_UPDATES'); ?></span>
								</a>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- span4 -->
	
	<div class="rsspan5 span5 pull-left">
		<?php if (!empty($this->stats)) { ?>
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<div class="rscomments-chart-type">
					<select name="stats_type" id="stats_type" onchange="rscomments_chart(this.value);">
						<?php echo JHtml::_('select.options', $this->types); ?>
					</select>
				</div>
				<h5><?php echo JText::_('COM_RSCOMMENTS_CHART_STATS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<div class="clearfix"></div>
					<div class="row-fluid" id="rscomments-chart"></div>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<h5><?php echo JText::_('COM_RSCOMMENTS_LATEST_COMMENTS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<table class="dashboard-block-table task-tbl">
						<tbody>
							<?php 
							if(!empty($this->latest_com)){
								foreach($this->latest_com as $comment) { 
							?>
								<tr>
									<td width="4%">
										<span class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText($comment->email.'<br />'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_NAME',$comment->name).'<br/>'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_SITE',$comment->website).'<br/>'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_IP',str_replace(':','&#058;',$comment->ip))); ?>">
											<a href="mailto:<?php echo $comment->email; ?>"> <img src="<?php echo JURI::root(); ?>administrator/components/com_rscomments/assets/images/info_medium.png" style="vertical-align:middle;" /></a>
										</span>
									</td>
									<td class="text-<?php  echo $comment->published == 1 ? 'success' : 'error';?>" width="80%">
										<strong><a class="text-<?php  echo $comment->published == 1 ? 'success' : 'error';?> <?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText($this->escape(RSCommentsHelper::cleanComment($comment->comment))); ?>" href="<?php echo JRoute::_('index.php?option=com_rscomments&task=comment.edit&IdComment='.$comment->IdComment); ?>">
										<?php echo !empty($comment->subject) ? $comment->subject : '<i>'.JText::_('COM_RSCOMMENTS_NO_TITLE').'</i>';?>
										</a></strong>
										<br />
										<?php echo RSCommentsHelper::showDate($comment->date); ?>
									</td>
									<td align="right" width="15%">
										<?php if ($comment->url) { ?>
										<a href="<?php echo JURI::root().base64_decode($comment->url);?>" target="_blank" class="btn btn-info btn-small"><i class="icon-eye-open"></i> <?php echo JText::_('COM_RSCOMMENTS_COMMENT_PREVIEW'); ?></a>
										<?php } ?>
									</td>								
								</tr>
							<?php 
								} 
							}else {	
							?>
								<tr><td colspan="3"><?php echo JText::_('COM_RSCOMMENTS_NO_COMMENTS');?></td></tr>
							<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td align="right" colspan="3"><a href="index.php?option=com_rscomments&amp;view=comments" class="btn btn-info btn-small"><?php echo JText::_('COM_RSCOMMENTS_VIEW_ALL_COMMENTS');?></a></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	
	</div><!-- span5 -->
	
	
	<div class="rsspan3 span3 pull-left rsj_margin">
		<div class="dashboard-container">
			<div class="dashboard-info">
				<span>
					<img src="components/com_rscomments/assets/images/rscomments.png" align="middle" alt="RSComments! logo"/>
				</span>
				<table class="dashboard-table">
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSCOMMENTS_INSTALLED_VERSION') ?> </strong></td>
						<td colspan="2"><b>RSComments! <?php echo $this->version; ?></b></td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSCOMMENTS_COPYRIGHT') ?> </strong></td>
						<td nowrap="nowrap">&copy; 2007 - <?php echo date('Y'); ?> <a href="http://www.rsjoomla.com" target="_blank">RSJoomla.com</a></td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSCOMMENTS_LICENSE') ?> </strong></td>
						<td nowrap="nowrap">GPL Commercial License</td>
					</tr>
					<tr>
						<td nowrap="nowrap" align="right"><strong><?php echo JText::_('COM_RSCOMMENTS_UPDATE_CODE') ?> </strong></td>
						<?php if (strlen($this->code) == 20) { ?>
						<td nowrap="nowrap" class="text-success"><?php echo $this->escape($this->code); ?></td>
						<?php } elseif ($this->code) { ?>
						<td nowrap="nowrap" class="text-error"><strong><?php echo $this->escape($this->code);?></strong></td>
						<?php } else { ?>
						<td nowrap="nowrap" class="missing-code">
							<?php if (RSCommentsHelper::isJ3()) { ?>
							<a href="index.php?option=com_config&view=component&component=com_rscomments&path=&return=<?php echo base64_encode(JURI::getInstance()); ?>">
							<?php } else { ?>
							<a class="modal" rel="{handler: 'iframe', size: {x: 875, y: 550}}" href="index.php?option=com_config&view=component&component=com_rscomments&path=&tmpl=component">
							<?php } ?>
								<?php echo JText::_('COM_RSCOMMENTS_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?>
							</a>
						</td>
						<?php } ?>
					</tr>
				</table>
			</div>
		</div>
	</div> <!-- span3 -->
</div><!-- row-fluid -->