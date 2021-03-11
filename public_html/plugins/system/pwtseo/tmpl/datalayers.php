<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

/** @var Form $form */
$form      = $displayData['form'];
$fieldSets = $form->getFieldsets();

$prefix    = 'pwtseo-datalayers-';
$templates = $displayData['templates'];
$languages = $displayData['languages'];
?>

<div class="pwtseo-datalayers container-fluid container-main">
	<div class="form-horizontal">
		<?php if (count($fieldSets))
		:
			?>
			<?php echo HTMLHelper::_('bootstrap.startTabSet', 'pwtseoTab', array('active' => $prefix . reset($fieldSets)->name)); ?>

			<?php foreach ($fieldSets as $fieldset)
			:
				?>
				<?php echo HTMLHelper::_('bootstrap.addTab', 'pwtseoTab', $prefix . $fieldset->name, $fieldset->label); ?>

				<div class="row-fluid" data-js-lang="<?php echo $fieldset->language ?>">
					<div class="span9">
						<?php echo $form->renderFieldset($fieldset->name); ?>
					</div>
					<div class="span3">
						<fieldset class="form-vertical">
							<div class="control-group">
								<div class="control-label">
									<label>
										<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_LANGUAGE') ?>
									</label>
								</div>

								<div class="controls">
									<div class="field-language">
										<?php echo $fieldset->language !== '*' ? LayoutHelper::render('joomla.content.language', $languages[$fieldset->language]) : Text::_('JALL'); ?>
										<br/>
									</div>
								</div>
							</div>
							<div class="control-group">
								<div class="control-label">
									<label>
										<?php echo Text::_('PLG_SYSTEM_PWTSEO_LABELS_TEMPLATE') ?>
									</label>
								</div>
								<div class="controls">
									<div class="field-language">
										<?php echo $fieldset->template ? $templates[$fieldset->template]->title : Text::_('JALL') ?>
									</div>
								</div>
							</div>
						</fieldset>
					</div>
				</div>

				<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
			<?php endforeach; ?>

			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
		<?php else

		:
			?>
		<div class="">
			<?php echo Text::_('PLG_SYSTEM_PWTSEO_NO_DATALAYERS') ?>
		</div>
		<?php endif; ?>
	</div>
</div>
