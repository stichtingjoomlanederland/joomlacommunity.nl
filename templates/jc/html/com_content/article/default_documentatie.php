<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);
?>



<div class="well">
	<div class="page-header">
		<div class="pull-right">
			<span class="label label-joomla2"><span class="icon-joomla"></span> Joomla 2.5</span>
			<span class="label label-joomla3"><span class="icon-joomla"></span> Joomla 3.0</span>
		</div>
		<?php if ($params->get('show_title')) : ?>
			<h1>
				<?php echo $this->escape($this->item->title); ?>
			</h1>
		<?php endif; ?>
	</div>
	<div class="item-content">
		<?php if ($canEdit) : ?>
			<div class="edit-buttons">
				<?php echo JLayoutHelper::render('joomla.content.icons', array('params' => $params, 'item' => $this->item, 'print' => false)); ?>
			</div>
		<?php endif; ?>
		<?php echo $this->item->text; ?>
	</div>
	<div class="articleinfo">
		<a class="btn btn-small btn-danger pull-right" data-toggle="modal" href="#verbetering">
			<span class="glyphicon glyphicon-warning-sign"></span> Verbetering doorgeven
		</a>
		<p class="text-muted"><strong>Gepubliceerd:</strong> <?php echo JHtml::_('date', $this->item->created, JText::_('j F Y')); ?>, <strong>aangepast:</strong> <?php echo JHtml::_('date', $this->item->modified, JText::_('j F Y')); ?><br/>
		Aan dit artikel hebben bijgedragen: <a href="#">Marieke</a>, <a href="#">Marijke</a></p>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="verbetering">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Verbetering / aanvulling doorgeven</h4>
			</div>
			<div class="modal-body">
				<p>Fout in het artikel? Aanvullingen of verbeteringen? Geef het door via onderstaand formulier. Hartelijk dank!</p>
				<form class="form-horizontal" role="form">
					<div class="form-group">
						<label for="artikel" class="col-3 control-label">Artikel</label>
						<div class="col-9">
							<p class="form-control-static"><?php echo $this->escape($this->item->title); ?></p>
<!-- 							<input type="text" class="form-control" id="artikel" placeholder="<?php echo $this->escape($this->item->title); ?>" disabled> -->
						</div>
					</div>
					<div class="form-group">
						<label for="naam" class="col-3 control-label">Naam</label>
						<div class="col-9">
							<input type="text" class="form-control" id="naam" placeholder="Naam">
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-3 control-label">E-mailadres</label>
						<div class="col-9">
							<input type="email" class="form-control" id="email" placeholder="Email">
						</div>
					</div>
					<div class="form-group">
						<label for="bericht" class="col-3 control-label">Bericht</label>
						<div class="col-9">
							<textarea class="form-control" rows="8"></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Sluit</button>
				<button type="button" class="btn btn-success">Verstuur</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dalog -->
</div><!-- /.modal -->

<?php
if (!empty($this->item->pagination) && $this->item->pagination) {
	echo $this->item->pagination;
}
?>

<?php echo $this->item->event->afterDisplayContent; ?>
