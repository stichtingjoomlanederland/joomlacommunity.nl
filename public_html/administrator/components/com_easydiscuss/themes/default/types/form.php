<?php
/**
* @package    EasyDiscuss
* @copyright  Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_GENERAL'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_POST_TYPES_TITLE', $postTypes->title); ?>

					<?php if ($postTypes->id) { ?>
						<?php echo $this->html('forms.textbox', 'alias', 'COM_EASYDISCUSS_POST_TYPES_ALIAS', $postTypes->alias, array(
							'attributes' => $postTypes->id ? 'readonly="readonly"' : ''
						)); ?>
					<?php } ?>

					<?php echo $this->html('forms.textbox', 'suffix', 'COM_EASYDISCUSS_POST_TYPES_SUFFIX', $postTypes->suffix,
						array(
							'size' => 7
						)
					); ?>

					<?php echo $this->html('forms.iconpicker', 'icon', 'COM_ED_POST_TYPES_ICON', $postTypes->icon, ''); ?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_ASSOCIATION'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					
					<?php echo $this->html('forms.dropdown', 'type', 'COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_TYPE', $postTypes->type,
						array(
							'global' => 'COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_GLOBAL',
							'category' => 'COM_EASYDISCUSS_POST_TYPES_ASSOCIATION_CATEGORY'
						), 'data-association-type');
					?>

					<div class="o-form-group <?php echo !$postTypes->type || $postTypes->type == 'global' ? ' t-hidden' : '';?>" data-type-category>
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POST_TYPES_SELECT_CATEGORIES'); ?>
						</div>

						<div class="col-md-7">
							<?php echo $categories; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $this->html('form.action', 'post_types', '', ''); ?>
<input type="hidden" name="id" value="<?php echo $postTypes->id ?>" />

</form>
