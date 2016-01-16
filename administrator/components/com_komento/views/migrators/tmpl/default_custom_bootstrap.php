<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

// prepare table selection
$tables = Komento::getHelper('database')->getTables();
foreach( $tables as &$table )
{
	$table = JHtml::_( 'select.option', $table, $table );
}
$tableSelection = JHtml::_( 'select.genericlist', $tables, 'migrate-table' );

$components = Komento::getHelper( 'components' )->getAvailableComponents();
foreach( $components as &$component )
{
	$component = JHtml::_( 'select.option', $component, $component );
}
$componentSelection = JHtml::_( 'select.genericlist', $components, 'migrate-component-filter' );
?>
<script type="text/javascript">
Komento.require().script('migrator.custom').done(function($) {
	$('.migrator-custom-data').implement('Komento.Controller.Migrator.Custom');
});
</script>
<div id="migrator-custom" migrator-type="custom" migration-type="custom" class="noshow migratorTable row-fluid">
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LAYOUT_MAIN' ); ?></legend>
				<table class="migrator-options admintable migrator-custom-data">
					<tr>
						<td class="key"><span>Table</span></td>
						<td><?php echo $tableSelection; ?></td>
					</tr>
					<tr>
						<td class="key"><span>Component</span></td>
						<td>
							<select id="migrate-column-component" class="table-columns"></select>
							<!-- <span class="migrate-checkbox-wrap">
								<input id="component-use-table-columns" type="checkbox" checked="checked" />
								<label for="component-use-table-columns">Use Table Columns </label>
							</span> -->
						</td>
					</tr>
					<tr>
						<td class="key"><span>Component Filter</span></td>
						<td>
							<?php echo $componentSelection; ?>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Content ID</span></td>
						<td>
							<select id="migrate-column-contentid" class="table-columns" data-required='true'></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Comment</span></td>
						<td>
							<select id="migrate-column-comment" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Date</span></td>
						<td>
							<select id="migrate-column-date" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Author ID</span></td>
						<td>
							<select id="migrate-column-authorid" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Name</span></td>
						<td>
							<select id="migrate-column-name" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Email</span></td>
						<td>
							<select id="migrate-column-email" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Homepage</span></td>
						<td>
							<select id="migrate-column-homepage" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Published</span></td>
						<td>
							<select id="migrate-column-published" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>IP</span></td>
						<td>
							<select id="migrate-column-ip" class="table-columns"></select>
						</td>
					</tr>
					<tr>
						<td class="key"><span>Number of comments per cycle</span></td>
						<td><input id="migrate-cycle" type="input" value="100" /></td>
					</tr>
				</table>

				<a href="javascript:void(0);" class="migrateButton button"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_START_MIGRATE' ); ?></a>
			</fieldset>
		</div>

		<div class="migratorProgress span6">
			<?php echo $this->loadTemplate( 'progress' ); ?>
		</div>
	</div>
</div>
