<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets();

$prefix = 'pwtseo-datalayers-';

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');
?>
<form action="<?php echo Route::_('index.php?option=com_pwtseo&layout=modal'); ?>" method="post" name="adminForm"
      id="adminForm">
    <button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('datalayersedit.apply');"></button>
    <button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('datalayersedit.save');"></button>
    <button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('datalayersedit.cancel');"></button>

    <div class="pwtseo-datalayers container-fluid container-main">
        <div class="form-horizontal">
			<?php if (count($fieldSets)): ?>
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'pwtseoTab', array('active' => $prefix . reset($fieldSets)->name)); ?>

				<?php foreach ($fieldSets as $fieldset): ?>
					<?php echo HTMLHelper::_('bootstrap.addTab', 'pwtseoTab', $prefix . $fieldset->name, $fieldset->label); ?>

                    <div class="row-fluid" data-js-lang="<?php echo $fieldset->language ?>">
                        <div class="span9">
							<?php echo $this->form->renderFieldset($fieldset->name); ?>
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
											<?php echo $fieldset->language !== '*' ? LayoutHelper::render('joomla.content.language', $this->form->languages[$fieldset->language]) : Text::_('JALL'); ?>
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
											<?php echo $fieldset->template ? $this->form->templates[$fieldset->template]->title : Text::_('JALL') ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

					<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
				<?php endforeach; ?>

				<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
			<?php else: ?>
                <div class="">
					<?php echo Text::_('PLG_SYSTEM_PWTSEO_NO_DATALAYERS') ?>
                </div>
			<?php endif; ?>
        </div>
    </div>

    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="jform[pwtseo][context]" value="<?php echo $this->context ?>"/>
    <input type="hidden" name="jform[pwtseo][context_id]" value="<?php echo $this->context_id ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
