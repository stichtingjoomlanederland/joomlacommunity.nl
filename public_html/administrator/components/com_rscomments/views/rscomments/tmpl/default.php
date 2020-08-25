<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 
JText::script('COM_RSCOMMENTS_CHART_COMMENTS');
?>

<?php if (!empty($this->stats)) { ?>
<script type="text/javascript">
	function drawChart() {
		var data = google.visualization.arrayToDataTable(
			<?php echo json_encode($this->stats); ?>
		);

		var options = { vAxis: {title: '<?php echo JText::_('COM_RSCOMMENTS_CHART_COMMENTS',true); ?>', 'format' : '0'}, legend: { position: 'none' } };
		var chart = new google.visualization.ColumnChart(document.getElementById('rscomments-chart'));
		chart.draw(data, options);
	}
</script>
<?php } ?>

<div class="<?php echo RSCommentsAdapterGrid::row(); ?>">
	<div class="<?php echo RSCommentsAdapterGrid::column(5); ?>">
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<h5><?php echo JText::_('COM_RSCOMMENTS_LATEST_COMMENTS'); ?></h5>
			</div>
			<div class="dashboard-block-content">
				<div class="dashboard-block-box">
					<table class="dashboard-block-table task-tbl">
						<tbody>
							<?php if (!empty($this->latest_com)) { ?>
							<?php foreach($this->latest_com as $comment) { ?>
							
							<tr>
								<td class="center" width="8%">
									<?php echo RSCommentsHelperAdmin::getAvatar($comment->uid, $comment->email, 40); ?>
								</td>
								<td class="rsc_comment_details">
									<?php $name = $comment->anonymous ? ($comment->name ? $comment->name : JText::_('COM_RSCOMMENTS_ANONYMOUS')) : $comment->name; ?>
									<?php echo JText::sprintf('COM_RSCOMMENTS_POSTED_A_COMMENT', $name); ?>
									<br>
									<span class="muted text-muted"><a href="<?php echo JRoute::_('index.php?option=com_rscomments&task=comment.edit&IdComment='.$comment->IdComment); ?>"><i class="fa fa-pencil"></i> <?php echo JText::_('COM_RSCOMMENTS_POSTED_A_COMMENT_EDIT'); ?></a></span>
									<span class="muted text-muted"><a href="<?php echo JURI::root().base64_decode($comment->url);?>#rscomment<?php echo $comment->IdComment; ?>" target="_blank"><i class="fa fa-eye"></i> <?php echo JText::_('COM_RSCOMMENTS_POSTED_A_COMMENT_VIEW'); ?></a></span>
									<span class="muted text-muted"><i class="fa fa-joomla"></i> <?php echo RSCommentsHelperAdmin::component($comment->option); ?></span>
									<span class="muted text-muted"><i class="fa fa-calendar"></i> <?php echo RSCommentsHelperAdmin::showDate($comment->date); ?></span>
								
								</td>
							</tr>
							<?php } ?>
							<tr>
								<td class="center text-center" colspan="3"><a href="index.php?option=com_rscomments&amp;view=comments" class="btn btn-info btn-small"><?php echo JText::_('COM_RSCOMMENTS_VIEW_ALL_COMMENTS');?></a></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
	<div class="<?php echo RSCommentsAdapterGrid::column(4); ?>">
		<?php if (!empty($this->stats)) { ?>
		<div class="dashboard-block">
			<div class="dashboard-block-head">
				<div class="rscomments-chart-type">
					<select name="stats_type" id="stats_type" onchange="rscomments_chart(this.value);" class="form-control form-control-sm">
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
	</div>
	
	<div class="<?php echo RSCommentsAdapterGrid::column(3); ?>">
		<ul class="<?php echo RSCommentsAdapterGrid::nav(); ?>">
			<li class="center active">
				<div class="dashboard-container">
					<div class="dashboard-info">
						<span>
							<?php echo JHtml::image('com_rscomments/rscomments.png', 'RSComments!', array('align' => 'middle'), true); ?>
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
									<a href="index.php?option=com_config&view=component&component=com_rscomments&path=&return=<?php echo base64_encode(JURI::getInstance()); ?>">
										<?php echo JText::_('COM_RSCOMMENTS_PLEASE_ENTER_YOUR_CODE_IN_THE_CONFIGURATION'); ?>
									</a>
								</td>
								<?php } ?>
							</tr>
						</table>
					</div>
				</div>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=comments'); ?>">
					<i class="fa fa-comments"></i> <?php echo JText::_('COM_RSCOMMENTS_COMMENTS'); ?>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=emoticons'); ?>">
					<i class="fa fa-smile-o"></i> <?php echo JText::_('COM_RSCOMMENTS_EMOTICONS'); ?>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=subscriptions'); ?>">
					<i class="fa fa-user"></i> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIPTIONS'); ?>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=groups'); ?>">
					<i class="fa fa-users"></i> <?php echo JText::_('COM_RSCOMMENTS_GROUP_PERMISSIONS'); ?>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=import'); ?>">
					<i class="fa fa-upload"></i> <?php echo JText::_('COM_RSCOMMENTS_IMPORT'); ?>
				</a>
			</li>
			<li>
				<a class="nav-link" href="<?php echo JRoute::_('index.php?option=com_rscomments&view=messages'); ?>">
					<i class="fa fa-envelope"></i> <?php echo JText::_('COM_RSCOMMENTS_MESSAGES'); ?>
				</a>
			</li>
		</ul>
	</div>
</div>