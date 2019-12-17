<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// No direct access.
defined('_JEXEC') or die;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.tooltip');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
	<?php if ($this->step == 2): ?>
		<?php echo LayoutHelper::render('pwtacl.legend.group'); ?>
	<?php endif; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="pwtacl" class="row-fluid">

		<?php if ($this->step == 1): ?>
			<div class="span12">
				<div class="well well-large">
					<legend><?php echo Text::_('COM_PWTACL_WIZARD_STEP1'); ?></legend>
					<p class="lead"><?php echo Text::_('COM_PWTACL_WIZARD_STEP1_DESC'); ?></p>
				</div>
			</div>
			<form action="<?php echo Route::_('index.php?option=com_pwtacl&view=wizard'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
				<div class="form-horizontal">
					<?php echo $this->form->renderFieldset('default'); ?>
				</div>

				<input type="submit" class="btn btn-large btn-success btn-block" value="<?php echo Text::_('COM_PWTACL_WIZARD_STEP1_SUBMIT'); ?>"/>
				<input type="hidden" name="task" value="wizard.groupSetup"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		<?php endif; ?>

		<?php if ($this->step == 2): ?>
			<div class="span12">
				<div class="well well-large">
					<legend><?php echo Text::_('COM_PWTACL_WIZARD_STEP2'); ?></legend>
					<p class="lead"><?php echo Text::_('COM_PWTACL_WIZARD_STEP2_DESC'); ?></p>
				</div>
			</div>
			<?php foreach ($this->components as $component): ?>
				<h3><?php echo $component->title; ?></h3>
				<table id="pwtacl" class="table table-bordered table-fixed-header managepermissions">
					<?php echo LayoutHelper::render('pwtacl.table.wizard.header'); ?>
					<?php echo LayoutHelper::render(
						'pwtacl.table.wizard.body',
						array(
							'assets' => $component->assets,
							'group'  => $this->group
						)
					); ?>
				</table>
			<?php endforeach; ?>
			<form action="<?php echo Route::_('index.php?option=com_pwtacl&view=wizard'); ?>" method="post" name="adminForm" id="item-form">
				<input type="submit" class="btn btn-large btn-success btn-block" value="<?php echo Text::_('COM_PWTACL_WIZARD_STEP2_SUBMIT'); ?>"/>
				<input type="hidden" name="groupid" value="<?php echo $this->group; ?>"/>
				<input type="hidden" name="task" value="wizard.finalize"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
		<?php endif; ?>

	</div>
</div>
