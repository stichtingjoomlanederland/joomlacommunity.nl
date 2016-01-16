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

$references 	= false;

if( $post )
{
	$references 	= $this->getFieldData( 'references' , $post->params );
}

if( $system->config->get( 'reply_field_references' ) ){
?>

<div class="tab-pane" id="referencesTab-<?php echo $composer->id; ?>">
	<div class="field-references">
		<p><?php echo JText::_( 'COM_EASYDISCUSS_URL_REFERENCES_DESC' );?></p>

		<ul class="attach-list unstyled mb-10">

		<?php if( isset( $references ) && $references ){ ?>
			<?php
				$total  = count( $references );

				for( $i = 0; $i < $total; $i++ )
				{

					$references[ $i ] 	= str_ireplace( array( '"' , "'" ) , '' , $references[ $i ] );
			?>
			<li>
				<input type="text" name="params_references[]" class="form-control" value="<?php echo $this->escape( $references[ $i ] ); ?>" />
				<?php if( $i != 0 ){ ?>
				<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" class="btn remove-url" style="display: inline-block;"><i class="icon-remove"></i> </a>
				<?php } else { ?>
				<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" style="display: none;" class="btn btn-danger remove-url"><i class="icon-remove"></i> </a>
				<?php } ?>
			</li>
			<?php } ?>
		<?php } else { ?>
			<li>
				<input type="text" name="params_references[]" class="form-control" />
				<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" style="display: inline-block;" class="btn btn-danger remove-url"><i class="icon-remove"></i> </a>
			</li>
		<?php } ?>
		</ul>
		<a href="javascript:void(0);" class="butt butt-default" onclick="discuss.reply.addURL(this);">
			<i class="i i-plus">
			</i> <?php echo JText::_( 'COM_EASYDISCUSS_REFERENCES_ADD_LINK_BUTTON' );?>
		</a>
	</div>
</div>
<?php } ?>
