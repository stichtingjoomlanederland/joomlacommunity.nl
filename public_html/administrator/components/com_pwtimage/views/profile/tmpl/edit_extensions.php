<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen');
HTMLHelper::_('script', 'jui/treeselectmenu.jquery.min.js', array('version' => 'auto', 'relative' => true));

Factory::getDocument()->addScriptDeclaration(<<<JS
	jQuery(document).ready(function (){
		jQuery('#jform_allMediaFields0').on('click', function() {
		  jQuery('#extensionselect-group').addClass('hidden');
		});
		jQuery('#jform_allMediaFields1').on('click', function() {
		  jQuery('#extensionselect-group').removeClass('hidden');
		});
	});
JS
);
?>
<?php echo $this->form->renderField('allMediaFields'); ?>
<div id="extensionselect-group" class="form-vertical control-group <?php echo (int) $this->form->getValue('allMediaFields') === 1 ? 'hidden' : ''; ?>">
	<div id="jform_extensionselect" class="controls">
		<?php if (!empty($this->extensions)) : ?>
			<div class="well well-small">
				<div class="form-inline">
				<span class="small"><?php echo Text::_('JSELECT'); ?>:
					<a id="treeCheckAll" href="javascript://"><?php echo Text::_('JALL'); ?></a>,
					<a id="treeUncheckAll" href="javascript://"><?php echo Text::_('JNONE'); ?></a>
				</span>
					<span class="width-20">|</span>
					<span class="small"><?php echo Text::_('COM_PWTIMAGE_EXPAND'); ?>:
					<a id="treeExpandAll" href="javascript://"><?php echo Text::_('JALL'); ?></a>,
					<a id="treeCollapseAll" href="javascript://"><?php echo Text::_('JNONE'); ?></a>
				</span>
					<input type="text" id="treeselectfilter" name="treeselectfilter" class="input-medium search-query pull-right" size="16"
					       autocomplete="off" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>" aria-invalid="false" tabindex="-1">
				</div>

				<div class="clearfix"></div>

				<hr class="hr-condensed" />

				<ul class="treeselect">
					<?php
                    $formExtensions = $this->form->getData()->get('extensions');
                    foreach ($this->extensions as $group => $extension) : ?>
							<li>
							<div class="treeselect-item pull-left">
								<label class="pull-left nav-header"><?php echo $group; ?></label>
							</div>
								<ul class="treeselect-sub">
									<?php foreach ($extension as $index => $item) : ?>
										<?php
										$checked   = '';

										if (in_array($index, $formExtensions))
										{
											$checked = 'checked="checked"';
										}
										?>
										<li>
										<div class="treeselect-item pull-left">
											<input type="checkbox"
											       class="pull-left novalidate"
											       id="<?php echo $item->identifier; ?>"
											       name="jform[extensions][]"
												<?php echo $checked; ?>
												   value="<?php echo $item->identifier; ?>" />
											<label for="<?php echo $item->identifier; ?>" class="pull-left">
												<?php echo str_replace('.', ' > ', $item->breadcrumb); ?>
											</label>
										</div>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
					<?php endforeach; ?>
				</ul>
				<div id="noresultsfound" style="display:none;" class="alert alert-no-items">
					<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
				</div>
				<div style="display:none;" id="treeselectmenu">
					<div class="pull-left nav-hover treeselect-menu">
						<div class="btn-group">
							<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-micro">
								<span class="caret"></span>
							</a>
							<ul class="dropdown-menu">
								<li class="nav-header"><?php echo Text::_('COM_PWTIMAGE_SUBITEMS'); ?></li>
								<li class="divider"></li>
								<li class=""><a class="checkall" href="javascript://"><span class="icon-checkbox" aria-hidden="true"></span> <?php echo Text::_('JSELECT'); ?></a>
								</li>
								<li><a class="uncheckall" href="javascript://"><span class="icon-checkbox-unchecked" aria-hidden="true"></span> <?php echo Text::_('COM_PWTIMAGE_DESELECT'); ?></a>
								</li>
								<div class="treeselect-menu-expand">
									<li class="divider"></li>
									<li><a class="expandall" href="javascript://"><span class="icon-plus" aria-hidden="true"></span> <?php echo Text::_('COM_PWTIMAGE_EXPAND'); ?></a></li>
									<li><a class="collapseall" href="javascript://"><span class="icon-minus" aria-hidden="true"></span> <?php echo Text::_('COM_PWTIMAGE_COLLAPSE'); ?></a></li>
								</div>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

