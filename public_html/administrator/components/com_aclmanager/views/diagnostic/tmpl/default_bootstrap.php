<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div id="aclmanager" class="diagnostic bootstrap">
	<div class="span2">
		<div id="sidebar">
			<div class="sidebar-nav">
				<ul class="nav nav-list" id="submenu">
					<li>
						<a href="index.php?option=com_aclmanager&amp;view=home"><?php echo JText::_('COM_CPANEL'); ?></a>
					</li>
					<?php if(JFactory::getUser()->authorise('aclmanager.diagnostic', 'com_aclmanager')):?>
					<li class="active">
						<a href="index.php?option=com_aclmanager&amp;view=diagnostic"><?php echo JText::_('COM_ACLMANAGER_SUBMENU_DIAGNOSTIC'); ?></a>
					</li>
					<?php endif;?>
					<?php if(JFactory::getUser()->authorise('core.admin', 'com_aclmanager')):?>
					<li>
						<a href="index.php?option=com_aclmanager&amp;view=liveupdate"><?php echo JText::_('COM_ACLMANAGER_SUBMENU_UPDATE'); ?></a>
					</li>
					<?php endif;?>
				</ul>
			</div>

		<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CHECKS'); ?></h3>
			<div class="sidebar-nav">
				<ul class="nav nav-list" id="submenu">
					<li class="<?php if ($this->orphanassets): ?>error<?php else:?>success<?php endif;?>">
						<a href="#orphanassets"><i class="icon-<?php if ($this->orphanassets): ?>warning<?php else:?>checkmark<?php endif;?>"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS'); ?></a>
					</li>
					<li class="<?php if ($this->missingassets): ?>error<?php elseif ($this->orphanassets): ?>error<?php else:?>success<?php endif;?>">
						<a href="#missingassets"><i class="icon-<?php if ($this->missingassets): ?>warning<?php elseif ($this->orphanassets): ?>minus-2<?php else:?>checkmark<?php endif;?>"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS'); ?></a>
					</li>
					<li class="<?php if ($this->assetissues): ?>error<?php elseif ($this->orphanassets || $this->missingassets): ?>error<?php else:?>success<?php endif;?>">
						<a href="#assetissues"><i class="icon-<?php if ($this->assetissues): ?>warning<?php elseif ($this->orphanassets || $this->missingassets): ?>minus-2<?php else:?>checkmark<?php endif;?>"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES'); ?></a>
					</li>
					<li class="<?php if ($this->adminconflicts): ?>error<?php else:?>success<?php endif;?>">
						<a href="#adminconflicts"><i class="icon-<?php if ($this->adminconflicts): ?>error<?php else:?>checkmark<?php endif;?>"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS'); ?></a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="span10">
		<!-- Begin orphanassets -->
		<form id="orphanassets" action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixOrphanAssets" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<div class="alert <?php if ($this->orphanassets): ?>alert-error<?php else:?>alert-success<?php endif;?>">
				<?php if ($this->orphanassets): ?>
					<h3>
						<?php echo(count($this->orphanassets));?>
						<?php if(count($this->orphanassets) == 1):?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_DETECTED'); ?>
						<?php else: ?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_DETECTED_PLURAL'); ?>
						<?php endif;?>
					</h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_DESC'); ?></p>
					<!-- Begin table -->
					<table id="global-aclmanager" class="table table-bordered table-striped diagnostictable">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt "><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2" class="center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft center"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
							</tr>
						</thead>
						<!-- End header -->
						<!-- Begin table rows -->
						<?php foreach ($this->orphanassets as $i => $asset) : ?>
							<!-- Start row -->
							<tr class="row<?php echo $i % 2; ?>">
								<!-- Title -->
								<td class="norule title brrgt">
									<div class="icons">
										<span class="type icon-16-<?php echo $asset->type; ?>"></span>
									</div>
									<div class="title">
										<?php echo $asset->title; ?>
									</div>
								</td>
								<!-- Current level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->level); ?>
									<?php endif;?>
								</td>
								<!-- Correct level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->correct_level); ?>
									<?php endif;?>

								</td>
								<!-- Current parent -->
								<td class="norule center brlft">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->parent); ?>
									<?php endif;?>
								</td>
								<!-- Correct parent -->
								<td class="norule center">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->correct_parent); ?>
									<?php endif;?>
								</td>
								<!-- Current rules -->
								<td class="norule center brlft">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php if ($asset->rules == ''):?>
											<em>empty</em>
										<?php else:?>
											<?php echo($asset->rules); ?>
										<?php endif; ?>
									<?php endif;?>
								</td>
								<!-- Correct rules -->
								<td class="norule center correctrules">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php echo($asset->correct_rules); ?>
									<?php endif;?>
								</td>
								<!-- Id -->
								<td class="norule center assetid">
									<?php echo $asset->id; ?>
								</td>
							</tr>
							<!-- End row -->
						<?php endforeach; ?>
						<!-- End table rows -->
					</table>
					<!-- End table -->
					<button type="submit" class="btn"><i class="icon-refresh"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_BUTTON') ?></button>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_DESC'); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<!-- End orphanassets -->

		<!-- Begin missingassets -->
		<form id="missingassets" action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixMissingAssets" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<div class="alert <?php if ($this->missingassets): ?>alert-error<?php elseif ($this->orphanassets): ?><?php else:?>alert-success<?php endif;?>">
				<?php if ($this->missingassets): ?>
					<h3>
						<?php echo(count($this->missingassets));?>
						<?php if(count($this->missingassets) == 1):?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DETECTED'); ?>
						<?php else: ?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DETECTED_PLURAL'); ?>
						<?php endif;?>
					</h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DESC'); ?></p>
					<!-- Begin table -->
					<table id="global-aclmanager" class="table table-bordered table-striped diagnostictable">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt "><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2" class="center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft center"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
							</tr>
						</thead>
						<!-- End header -->
						<!-- Begin table rows -->
						<?php foreach ($this->missingassets as $i => $asset) : ?>
							<!-- Start row -->
							<tr class="row<?php echo $i % 2; ?>">
								<!-- Title -->
								<td class="norule title brrgt">
									<div class="icons">
										<span class="type icon-16-<?php echo $asset->type; ?>"></span>
									</div>
									<div class="title">
										<?php echo $asset->title; ?>
									</div>
								</td>
								<!-- Current level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->level); ?>
									<?php endif;?>
								</td>
								<!-- Correct level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->correct_level); ?>
									<?php endif;?>

								</td>
								<!-- Current parent -->
								<td class="norule center brlft">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->parent); ?>
									<?php endif;?>
								</td>
								<!-- Correct parent -->
								<td class="norule center">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->correct_parent); ?>
									<?php endif;?>
								</td>
								<!-- Current rules -->
								<td class="norule center brlft">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php if ($asset->rules == ''):?>
											<em>empty</em>
										<?php else:?>
											<?php echo($asset->rules); ?>
										<?php endif; ?>
									<?php endif;?>
								</td>
								<!-- Correct rules -->
								<td class="norule center correctrules">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php echo($asset->correct_rules); ?>
									<?php endif;?>
								</td>
								<!-- Id -->
								<td class="norule center assetid">
									<?php echo $asset->id; ?>
								</td>
							</tr>
							<!-- End row -->
						<?php endforeach; ?>
						<!-- End table rows -->
					</table>
					<!-- End table -->
					<button type="submit" class="btn"><i class="icon-refresh"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_BUTTON') ?></button>
				<?php elseif ($this->orphanassets): ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXFIRST'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DESC'); ?></p>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DESC'); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<!-- End missingassets -->

		<!-- Begin assetissues -->
		<form id="assetissues" action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixAssetIssues" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<div class="alert <?php if ($this->assetissues): ?>alert-error<?php elseif ($this->orphanassets || $this->missingassets): ?><?php else:?>alert-success<?php endif;?>">
				<?php if ($this->assetissues): ?>
					<h3>
						<?php echo(count($this->assetissues));?>
						<?php if(count($this->assetissues) == 1):?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DETECTED'); ?>
						<?php else: ?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DETECTED_PLURAL'); ?>
						<?php endif;?>
					</h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DESC'); ?></p>
					<!-- Begin table -->
					<table id="global-aclmanager" class="table table-bordered table-striped diagnostictable">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt "><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2" class="center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft center"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap center"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
							</tr>
						</thead>
						<!-- End header -->
						<!-- Begin table rows -->
						<?php foreach ($this->assetissues as $i => $asset) : ?>
							<!-- Start row -->
							<tr class="row<?php echo $i % 2; ?>">
								<!-- Title -->
								<td class="norule title brrgt">
									<div class="icons">
										<span class="type icon-16-<?php echo $asset->type; ?>"></span>
									</div>
									<div class="title">
										<?php echo $asset->title; ?>
									</div>
								</td>
								<!-- Current level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->level); ?>
									<?php endif;?>
								</td>
								<!-- Correct level -->
								<td class="norule center">
									<?php if($asset->level != $asset->correct_level):?>
										<?php echo($asset->correct_level); ?>
									<?php endif;?>

								</td>
								<!-- Current parent -->
								<td class="norule center brlft">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->parent); ?>
									<?php endif;?>
								</td>
								<!-- Correct parent -->
								<td class="norule center">
									<?php if($asset->parent != $asset->correct_parent):?>
										<?php echo($asset->correct_parent); ?>
									<?php endif;?>
								</td>
								<!-- Current rules -->
								<td class="norule center brlft">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php if ($asset->rules == ''):?>
											<em>empty</em>
										<?php else:?>
											<?php echo($asset->rules); ?>
										<?php endif; ?>
									<?php endif;?>
								</td>
								<!-- Correct rules -->
								<td class="norule center correctrules">
									<?php if($asset->rules != $asset->correct_rules):?>
										<?php echo($asset->correct_rules); ?>
									<?php endif;?>
								</td>
								<!-- Id -->
								<td class="norule center assetid">
									<?php echo $asset->id; ?>
								</td>
							</tr>
							<!-- End row -->
						<?php endforeach; ?>
						<!-- End table rows -->
					</table>
					<!-- End table -->
					<button type="submit" class="btn"><i class="icon-refresh"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_BUTTON') ?></button>
				<?php elseif ($this->orphanassets || $this->missingassets): ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXFIRST'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DESC'); ?></p>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DESC'); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<!-- End assetissues -->

		<!-- Begin adminconflicts -->
		<form id="adminconflicts" action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixAdminConflicts" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<div class="alert <?php if ($this->adminconflicts): ?>alert-error<?php else:?>alert-success<?php endif;?>">
				<?php if ($this->adminconflicts): ?>
					<h3>
						<?php echo(count($this->adminconflicts));?>
						<?php if(count($this->adminconflicts) == 1):?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_DETECTED'); ?>
						<?php else: ?>
							<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_DETECTED_PLURAL'); ?>
						<?php endif;?>
					</h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_DESC'); ?></p>
					<!-- Begin table -->
					<table id="global-aclmanager" class="table table-bordered table-striped diagnostictable">
						<!-- Begin header -->
						<thead>
							<tr>
								<th><?php echo JText::_('COM_ACLMANAGER_SUBMENU_GROUP'); ?></th>
								<th width="5%" class="center"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
						</thead>
						<!-- End header -->
						<!-- Begin table rows -->
						<?php foreach ($this->adminconflicts as $i => $group) : ?>
							<!-- Start row -->
							<tr class="row<?php echo $i % 2; ?>">
								<!-- Title -->
								<td class="norule title">
									<div class="icons">
										<span class="type icon-16-groups"></span>
									</div>
									<div class="title">
										<?php echo $group->title; ?>
									</div>
								</td>
								<!-- Id -->
								<td class="norule center">
									<?php echo $group->id; ?>
								</td>
							</tr>
							<!-- End row -->
						<?php endforeach; ?>
						<!-- End table rows -->
					</table>
					<!-- End table -->
					<button type="submit" class="btn"><i class="icon-refresh"></i> <?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_BUTTON') ?></button>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_DESC'); ?></p>
				<?php endif; ?>
			</div>
		</form>
		<!-- End adminconflicts -->

		<!-- Begin rebuild -->
		<form action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.rebuild" />
			<?php echo JHtml::_('form.token'); ?>
		</form>
		<!-- End rebuild -->
	</div>
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>