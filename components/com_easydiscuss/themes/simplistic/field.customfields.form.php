<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<div class="discuss-fields form-horizontal">
<?php if( !empty($items) ){ ?>
	<?php foreach( $items as $item ) { ?>
		<?php if( $item->acl_id == DISCUSS_CUSTOMFIELDS_ACL_INPUT ){ ?>

			<input type="hidden" name="customFields[]" value="<?php echo $item->id ?>">
			<?php $value = ( !empty( $item->value ) ) ? unserialize( $item->value ) : array(); ?>

			<div class="controls-row">

				<div class="control-group">

						<label for="inputtext1" class="control-label"><?php echo $this->escape($item->title) ?>:</label>

						<!-- @php text field -->
						<?php if( $item->type == 'text' ) { ?>
							<?php $textHolders = unserialize($item->params); ?>
							<?php foreach( $textHolders as $textHolder ){ ?>
							<div class="controls">
								<input id="inputtext1" class="" type="text" placeholder="<?php echo $this->escape($textHolder) ?>" name="customFieldValue_<?php echo $item->id ?>[]" value="<?php echo $this->escape(( empty($value)? '' : array_shift($value)) ) ?>" />
							</div>
							<?php } ?>
						<?php } ?>

						<!-- @php textarea field -->
						<?php if( $item->type == 'area' ) { ?>
							<?php $textAreaHolders = unserialize($item->params); ?>
							<?php foreach( $textAreaHolders as $textAreaHolder ){ ?>
							<div class="controls">
								<?php $value = (empty($value)? '' : array_shift($value)) ?>
								<textarea id="inputtext2" rows="3" class="" placeholder="<?php echo $this->escape($textAreaHolder) ?>" name="customFieldValue_<?php echo $item->id ?>[]" value="<?php echo $this->escape($value) ?>"><?php echo $this->escape($value) ?></textarea>
							</div>
							<?php } ?>
						<?php } ?>

						<!-- @php radio -->
						<?php if( $item->type == 'radio' ) { ?>
							<?php $radioLists = unserialize($item->params);	?>
							<div class="controls">
								<?php foreach( $radioLists as $id => $radioList ){ ?>
									<label class="radio">
										<?php echo $radioList ?>
										<input type="radio" name="customFieldValue_<?php echo $item->id ?>[]" id="optionsRadios<?php echo $id; ?>" value="<?php echo $this->escape((empty($radioList)? '' : $radioList)) ?>" <?php echo (in_array($radioList, $value)) ? 'checked="checked"' : '' ?> /><br />
									</label>
								<?php } ?>
							</div>
						<?php } ?>

						<!-- @php checkbox -->
						<?php if( $item->type == 'check' ) { ?>
							<?php $checkLists = unserialize($item->params);	?>
							<div class="controls">
							<?php foreach( $checkLists as $checkList ){ ?>
								<!-- <div> -->
									<label class="checkbox">
										<?php echo $checkList ?>
										<input type="checkbox" id="inlineCheckbox1" name="customFieldValue_<?php echo $item->id ?>[]" value="<?php echo $this->escape((empty($checkList)? '' : $checkList)) ?>" <?php echo (in_array($checkList, $value)) ? 'checked="checked"' : '' ?> /><br />
									</label>
								<!-- </div> -->
							<?php } ?>
							</div>
						<?php } ?>

						<!-- @php select list and multiple select list -->
						<?php if( $item->type == 'select' || $item->type == 'multiple' ) { ?>
							<?php $selectLists = unserialize($item->params);	?>
							<div class="controls">
								<select <?php echo ( $item->type == 'multiple' ) ? 'multiple="true"' : '' ?> name="customFieldValue_<?php echo $item->id ?>[]">

								<?php if( $item->type == 'select' ){ ?>
									<!-- Display a default value for select list -->
									<option value="defaultList"><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_PLEASE_SELECT'); ?></option>
								<?php } ?>

								<?php foreach( $selectLists as $selectList ){ ?>
									<option value="<?php echo $this->escape((empty($selectList)? '' : $selectList)) ?>" <?php echo (in_array($selectList, $value)) ? 'selected="selected"' : '' ?> ><?php echo $this->escape((empty($selectList)? '' : $selectList)) ?></option>
								<?php } ?>
								</select>
							</div>
						<?php } ?>

				</div>

			</div>
		<?php } ?>
	<?php } ?>
<?php } ?>
</div>
