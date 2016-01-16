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
$pendingCount = Komento::getModel( 'comments' )->getCount( 'all', 'all', array( 'published' => 2 ) );
$reportsCount = Komento::getModel( 'reports', true )->getTotal();
?>
<script type="text/javascript">
Komento.ready(function($){

	Komento.ajax('admin.views.komento.getVersion', {}, {
		success: function( html ) {
			$('[data-komento-version]').html('<div class="kmt-version">' + html + '</div>');
		}
	});
});
</script>

<div class="row">
	<div class="col-md-9">
		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_('COM_KOMENTO_COMMENTS'); ?></div>
			<div class="panel-body">
				<ul class="panel-tab reset-ul">
					<li class="active">
						<a href="#latest" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_LATEST' ); ?></a>
					</li>
					<li>
						<a href="#pending" data-foundry-toggle="tab"><?php echo JText::_( 'COM_KOMENTO_MODERATE' );?></a>
					</li>
				</ul>

				<div class="tab-pane active" id="latest">
					<table class="panel-table table table-striped" cellspacing="1">
						<tr>
							<th><?php echo JText::_('COM_KOMENTO_COMMENTS'); ?></th>
							<th><?php echo JText::_('COM_KOMENTO_COMPONENT_TITLE'); ?></th>
							<th><?php echo JText::_('COM_KOMENTO_COLUMN_ARTICLEID'); ?></th>
						</tr>
						<?php if ($this->comments) { ?>
							<?php foreach ($this->comments as $comment) { ?>
							<tr>
								<td width="80%"><?php echo $comment->comment; ?></td>
								<td><?php echo $comment->componenttitle; ?></td>
								<td><?php if ($comment->extension) { ?>
									<a href="<?php echo $comment->pagelink; ?>" target=_blank><?php echo $comment->contenttitle; ?></a>
									<?php } else { ?>
									<span class="error"><?php echo $comment->contenttitle; ?></span>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td><?php echo JText::_('COM_KOMENTO_COMMENTS_NO_COMMENT'); ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>

				<div class="tab-pane" id="pending">
					<table class="panel-table table table-striped" cellspacing="1">
						<tr>
							<th><?php echo JText::_('COM_KOMENTO_COMMENTS'); ?></th>
							<th><?php echo JText::_('COM_KOMENTO_COMPONENT_TITLE'); ?></th>
							<th><?php echo JText::_('COM_KOMENTO_COLUMN_ARTICLEID'); ?></th>
						</tr>
						<?php if ($this->pendings) { ?>
							<?php foreach ($this->pendings as $pending) { ?>
							<tr>
								<td width="80%"><?php echo $pending->comment; ?></td>
								<td><?php echo $pending->componenttitle; ?></td>
								<td><?php if ($pending->extension) { ?>
									<a href="<?php echo $pending->pagelink; ?>" target=_blank><?php echo $pending->contenttitle; ?></a>
									<?php } else { ?>
									<span class="error"><?php echo $pending->contenttitle; ?></span>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>
						<?php } else { ?>
							<tr>
								<td><?php echo JText::_('COM_KOMENTO_COMMENTS_NO_COMMENT'); ?></td>
							</tr>
						<?php } ?>

					</table>
				</div>
			</div>
		</fieldset>

	</div>

	<div class="col-md-3">
		<fieldset class="panel">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_DATABASE_INFO' ); ?></div>
			<div class="panel-body">
				<table id="kmt-panel" width="100%">
					<tr>
						<td valign="top" style="padding:10px 10px 10px 0;">
							<?php echo $this->loadTemplate( 'right' ); ?>
						</td>
					</tr>
				</table>
				<div style="text-align: right;margin: 10px 5px 0 0;">
					<?php echo JText::_('Komento is a product of <a href="http://stackideas.com" target="_blank">StackIdeas</a>');?>
				</div>

			</div>
		</fieldset>

	</div>
</div>


