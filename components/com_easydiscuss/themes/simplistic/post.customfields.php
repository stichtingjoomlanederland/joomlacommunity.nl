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


<?php if( $system->config->get( 'main_customfields' ) ){ ?>
	<?php $myFields = $post->getMyCustomFields( $post->id, DISCUSS_CUSTOMFIELDS_ACL_VIEW ); ?>
	<?php if( !empty($myFields) ){ ?>
	<?php
		$displayField = false;
		foreach( $myFields as $myField )
		{
			if( isset( $myField->value ) )
			{
				$displayField = true;
			}
		}
	?>
		<?php if( $displayField ){ ?>
			<div class="discuss-fields">
				<?php foreach( $myFields as $myField ){ ?>
					<?php if( $myField->acl_id == DISCUSS_CUSTOMFIELDS_ACL_VIEW ){ ?>
						<?php if( !empty($myField->value) ){ ?>
							<div class="controls controls-row">
								<div class="span2 discuss-field-title">
									<label><?php echo $myField->title ?>:</label>
								</div>
								<div class="span4">
									<?php if( !empty( $myField->value ) ){ ?>

										<?php if( $myField->type == 'area' ){ ?>
											<label><?php echo nl2br( implode( ', ', unserialize($myField->value) ) ) ?></label>
										<?php }else{ ?>
											<label><?php echo implode( ', ', unserialize($myField->value) ) ?></label>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } ?>
<?php } ?>

