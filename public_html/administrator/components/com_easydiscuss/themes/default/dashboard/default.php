<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-lg-7">
			<div class="db-activity">
				<div class="db-activity-head">
					<b><?php echo JText::_('COM_ED_ACTIVITIES_THIS_WEEK');?></b>
				</div>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane in active" id="graphPosts" aria-labelledby="graphPosts-tab">
						<div class="db-stream db-stream-graph">
							<canvas id="graph-area" />
						</div>
					</div>
				</div>
			</div>
			<div class="db-activity t-mt--lg">
				<div class="db-activity-head">
					<b><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_RECENT_ACTIVITIES');?></b>
				</div>

				<ul class="db-activity-filter">
					<li class="active">
						<a href="#posts" id="posts-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS');?></a>
					</li>
					<li>
						<a href="#month" id="month-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS_MONTH');?></a>
					</li>
					<li>
						<a href="#category" id="category-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYDISCUSS_FILTER_POSTS_CATEGORY');?></a>
					</li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane in active" id="posts" aria-labelledby="posts-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area2" />
							</div>
							<div id="js-legend2" class="chart-legend"></div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="month" aria-labelledby="month-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area3" />
							</div>
							<div id="js-legend3" class="chart-legend"></div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane" id="category" aria-labelledby="category-tab">
						<div class="db-stream db-stream-graph db-stream--chart">
							<div id="canvas-holder">
								<canvas id="chart-area4" />
							</div>
							<div id="js-legend4" class="chart-legend"></div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="col-lg-5">

			<div class="panel t-d--none" data-version-checks>
				<div class="panel-body">
					<div class="l-stack t-text--center">
						<div class="l-stack">
							<div>
								<div>You are running on outdated version of EasyDiscuss</div>
							</div>
								
							<div>Installed version: <b><?php echo $version;?></b></div>

							<div class="t-text--success">
								Latest Version Available: <a href="https://stackideas.com/changelog/easydiscuss" class="t-text--success" target="_blank" style="text-decoration: underline;"><b><span data-version></span></a></b>
							</div>
							
							<a href="<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&controller=system&task=upgrade" class="o-btn o-btn--primary">
								<b>
									<i class="fa fa-bolt"></i>&nbsp; Update EasyDiscuss
								</b>
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="panel">
				<div class="panel-head t-my--no t-pb--no t-pl--no">
					<div class="t-d--flex">
						<div class="t-flex-grow--1">
							<b class="panel-head-title">Statistics</b>
						</div>
					</div>
				</div>
				<div class="panel-body t-bg--100 t-px--md t-m--no">
					
					<div class="l-stack l-spaces--xs">
						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=posts'),
							'icon' => 'fas fa-file t-text--success',
							'title' => 'COM_EASYDSICUSS_STATS_POSTS',
							'count' => $totalPosts
						]); ?>

						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=categories'),
							'icon' => 'fas fa-folder-open t-text--success',
							'title' => 'COM_EASYDISCUSS_STATS_CATEGORIES',
							'count' => $totalCategories
						]); ?>

						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=tags'),
							'icon' => 'fas fa-tags t-text--success',
							'title' => 'COM_EASYDISCUSS_STATS_TAGS',
							'count' => $totalTags
						]); ?>

						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=users'),
							'icon' => 'fas fa-user t-text--success',
							'title' => 'COM_EASYDISCUSS_STATS_USERS',
							'count' => $totalUsers
						]); ?>

						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=roles'),
							'icon' => 'fas fa-user-secret t-text--success',
							'title' => 'COM_EASYDISCUSS_STATS_ROLES',
							'count' => $totalUserRoles
						]); ?>

						<?php echo $this->output('admin/dashboard/stats', [
							'permalink' => JRoute::_('index.php?option=com_easydiscuss&view=types'),
							'icon' => 'fas fa-ticket-alt t-text--success',
							'title' => 'COM_EASYDISCUSS_STATS_POST_TYPES',
							'count' => $totalTypes
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="discuss" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="discuss" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
