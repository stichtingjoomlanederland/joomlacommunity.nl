<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo Route::_('index.php?option=com_pwtacl&view=assets'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		<?php echo LayoutHelper::render('pwtacl.legend.' . $this->type); ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

		<?php if ($this->group || $this->user): ?>
			<table id="pwtacl" class="table table-bordered table-fixed-header">
				<?php echo LayoutHelper::render('pwtacl.table.assets.header'); ?>
				<?php echo LayoutHelper::render('pwtacl.table.assets.body', [
					'assets' => $this->assets,
					'group'  => $this->group
				]); ?>
			</table>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php else: ?>
			<div class="well">
				<h3 class="text-center">
					<?php if ($this->type === 'group'): ?>
						<?php echo Text::_('COM_PWTACL_ASSETS_SELECT_GROUP'); ?>
					<?php elseif ($this->type === 'user'): ?>
						<?php echo Text::_('COM_PWTACL_ASSETS_SELECT_USER'); ?>
					<?php endif; ?>
				</h3>
			</div>
		<?php endif; ?>
	</div>

	<?php echo HTMLHelper::_(
		'bootstrap.renderModal',
		'importModal',
		[
			'title'  => Text::_('COM_PWTACL_TOOLBAR_IMPORT'),
			'footer' => $this->loadTemplate('import_footer'),
		],
		$this->loadTemplate('import_body')
	); ?>
	<?php echo HTMLHelper::_(
		'bootstrap.renderModal',
		'copyModal',
		[
			'title'  => Text::_('COM_PWTACL_TOOLBAR_COPY'),
			'footer' => $this->loadTemplate('copy_footer'),
		],
		$this->loadTemplate('copy_body')
	); ?>

	<input type="hidden" name="option" value="com_pwtacl"/>
	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
