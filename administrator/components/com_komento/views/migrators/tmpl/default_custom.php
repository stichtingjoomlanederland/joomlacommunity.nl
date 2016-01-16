<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
	<div class="row clearfix">
		<div class="col-md-6 migrate-setup">
			<fieldset class="panel">
				<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_LAYOUT_MAIN' ); ?></div>
				<div class="panel-body migrator-options admintable migrator-custom-data form-horizontal">
					<div class="form-group">
						<label class="col-md-5 control-label">Table</label>
						<div class="col-md-7"><?php echo $tableSelection; ?></div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Component</label>
						<div class="col-md-7">
							<select id="migrate-column-component" class="table-columns"></select>
							<!-- <span class="migrate-checkbox-wrap">
								<input id="component-use-table-columns" type="checkbox" checked="checked" />
								<label for="component-use-table-columns">Use Table Columns </label>
							</span> -->
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Component Filter</label>
						<div class="col-md-7">
							<?php echo $componentSelection; ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Content ID</label>
						<div class="col-md-7">
							<select id="migrate-column-contentid" class="table-columns" data-required='true'></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Comment</label>
						<div class="col-md-7">
							<select id="migrate-column-comment" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Date</label>
						<div class="col-md-7">
							<select id="migrate-column-date" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Author ID</label>
						<div class="col-md-7">
							<select id="migrate-column-authorid" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Name</label>
						<div class="col-md-7">
							<select id="migrate-column-name" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Email</label>
						<div class="col-md-7">
							<select id="migrate-column-email" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Homepage</label>
						<div class="col-md-7">
							<select id="migrate-column-homepage" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Published</label>
						<div class="col-md-7">
							<select id="migrate-column-published" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">IP</label>
						<div class="col-md-7">
							<select id="migrate-column-ip" class="table-columns"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-5 control-label">Number of comments per cycle</label>
						<div class="col-md-7"><input id="migrate-cycle" type="input" value="100" /></div>
					</div>
				</div>
				<div class="panel-foot">
					<a href="javascript:void(0);" class="migrateButton btn btn-success"><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_START_MIGRATE' ); ?></a>
				</div>
			</fieldset>
		</div>

		<div class="col-md-6 migrate-progress">
			<?php echo $this->loadTemplate( 'progress' ); ?>
		</div>
	</div>
</div>
