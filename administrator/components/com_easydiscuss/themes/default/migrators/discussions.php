<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm">

	<div class="row">
		<div class="col-md-6">
				<div class="panel">
					<div class="panel-head">
					<a href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' ); ?></h6>
					</a>
				</div>

					<div id="option01" class="panel-body">
						<div class="form-horizontal">
						<fieldset>

							<?php if ($this->discussionsExists()) { ?>
							<p><?php echo JText::_('This migration tool allows you to migrate forum categories and posts from Discussion component.');?></p>
							<?php } else { ?>
							<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_NOT_INSTALLED' ); ?></p>
							<?php } ?>
							<input type="button" class="btn btn-success migrator-button" onclick="runMigration('discussions');" value="<?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL');?>" />
						</fieldset>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<div class="panel-head">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_PROGRESS' ); ?></h6>
				</div>

				<div id="option01" class="panel-body">
					<fieldset>
						<ul id="migrator-discussions-log" style="max-height: 170px; overflow-y:scroll;list-style:none;">
						</ul>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

</form>
