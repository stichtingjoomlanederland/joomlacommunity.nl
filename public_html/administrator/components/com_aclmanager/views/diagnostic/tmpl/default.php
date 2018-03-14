<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div id="aclmanager" class="diagnostic">
	<div class="width-70 fltlft">
		<!-- Begin orphanassets -->
		<form action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixOrphanAssets" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<fieldset id="orphanassets" class="adminform <?php if ($this->orphanassets): ?>issues<?php else:?>fixed<?php endif;?>">
				<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS'); ?></legend>
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
					<table id="global-aclmanager" class="adminlist aclmanager">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt"><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
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
										<?php echo $asset->title; ?> <br/><small>(<?php echo $asset->name; ?>)</small>
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
						<!-- Begin footer -->
						<tfoot>
							<tr>
								<td colspan="8">
									<button type="submit">
										<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_BUTTON') ?>
									</button>
								</td>
							</tr>
						</tfoot>
						<!-- End footer -->
					</table>
					<!-- End table -->
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS_DESC'); ?></p>
				<?php endif; ?>
			</fieldset>
		</form>
		<!-- End orphanassets -->

		<!-- Begin missingassets -->
		<form action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixMissingAssets" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<fieldset id="missingassets" class="adminform <?php if ($this->missingassets): ?>issues<?php elseif ($this->orphanassets): ?>fixfirst<?php else:?>fixed<?php endif;?>">
				<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS'); ?></legend>
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
					<table id="global-aclmanager" class="adminlist aclmanager">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt"><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
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
										<?php echo $asset->title; ?> <br/><small>(<?php echo $asset->name; ?>)</small>
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
						<!-- Begin footer -->
						<tfoot>
							<tr>
								<td colspan="8">
									<button type="submit">
										<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_BUTTON') ?>
									</button>
								</td>
							</tr>
						</tfoot>
						<!-- End footer -->
					</table>
					<!-- End table -->
				<?php elseif ($this->orphanassets): ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXFIRST'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DESC'); ?></p>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS_DESC'); ?></p>
				<?php endif; ?>
			</fieldset>
		</form>
		<!-- End missingassets -->

		<!-- Begin assetissues -->
		<form action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixAssetIssues" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<fieldset id="assetissues" class="adminform <?php if ($this->assetissues): ?>issues<?php elseif ($this->orphanassets || $this->missingassets): ?>fixfirst<?php else:?>fixed<?php endif;?>">
				<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES'); ?></legend>
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
					<table id="global-aclmanager" class="adminlist aclmanager">
						<!-- Begin header -->
						<thead>
							<tr>
								<th rowspan="2" width="27%" class="brrgt"><?php echo JText::_('COM_ACLMANAGER_TABLE_ASSET_TITLE'); ?></th>
								<th colspan="2"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_LEVEL'); ?></th>
								<th colspan="2" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_PARENT'); ?></th>
								<th colspan="2" width="40%" class="brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_RULE'); ?></th>
								<th rowspan="2" width="5%" class="nowrap brlft"><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
							</tr>
							<tr>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
								<th class="nowrap brlft"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CURRENT'); ?></th>
								<th class="nowrap"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CORRECT'); ?></th>
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
										<?php echo $asset->title; ?> <br/><small>(<?php echo $asset->name; ?>)</small>
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
						<!-- Begin footer -->
						<tfoot>
							<tr>
								<td colspan="8">
									<button type="submit">
										<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_BUTTON') ?>
									</button>
								</td>
							</tr>
						</tfoot>
						<!-- End footer -->
					</table>
					<!-- End table -->
				<?php elseif ($this->orphanassets || $this->missingassets): ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_FIXFIRST'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DESC'); ?></p>
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES_DESC'); ?></p>
				<?php endif; ?>
			</fieldset>
		</form>
		<!-- End assetissues -->

		<!-- Begin adminconflicts -->
		<form action="index.php" method="post" id="adminForm">
			<input type="hidden" name="option" value="com_aclmanager" />
			<input type="hidden" name="task" value="diagnostic.fixAdminConflicts" />
			<?php echo JHtml::_('form.token'); ?>
			<!-- Begin fieldset -->
			<fieldset id="adminconflicts" class="adminform <?php if ($this->adminconflicts): ?>issues<?php else:?>fixed<?php endif;?>">
				<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS'); ?></legend>
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
					<table id="global-aclmanager" class="adminlist aclmanager">
						<!-- Begin header -->
						<thead>
							<tr>
								<th><?php echo JText::_('COM_ACLMANAGER_SUBMENU_GROUP'); ?></th>
								<th width="5%" ><?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
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
						<!-- Begin footer -->
						<tfoot>
							<tr>
								<td colspan="2">
									<button type="submit">
										<?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_BUTTON') ?>
									</button>
								</td>
							</tr>
						</tfoot>
						<!-- End footer -->
					</table>
					<!-- End table -->
				<?php else: ?>
					<h3><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_OK'); ?></h3>
					<p><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS_DESC'); ?></p>
				<?php endif; ?>
			</fieldset>
		</form>
		<!-- End adminconflicts -->
	</div>

	<!-- Begin sidebar -->
	<div class="width-30 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_CHECKS'); ?></legend>
			<img class="fieldseticon" src="components/com_aclmanager/assets/images/diagnostic.png"/>
			<ul>
				<li class="rule">
					<span class="icon <?php if ($this->orphanassets): ?>denied<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="#orphanassets"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ORPHAN_ASSETS'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->missingassets): ?>denied<?php elseif ($this->orphanassets): ?>unset<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="#missingassets"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_MISSING_ASSETS'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->assetissues): ?>denied<?php elseif ($this->orphanassets || $this->missingassets): ?>unset<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="#assetissues"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ASSET_ISSUES'); ?></a>
					</span>
				</li>
				<li class="rule">
					<span class="icon <?php if ($this->adminconflicts): ?>denied<?php else:?>allowed<?php endif;?>"></span>
					<span class="legend">
						<a href="#adminconflicts"><?php echo JText::_('COM_ACLMANAGER_DIAGNOSTIC_ADMIN_ACCESS_CONFLICTS'); ?></a>
					</span>
				</li>
			</ul>
		</fieldset>
		<div class="clr"></div>
	</div>
	<!-- End sidebar -->
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>