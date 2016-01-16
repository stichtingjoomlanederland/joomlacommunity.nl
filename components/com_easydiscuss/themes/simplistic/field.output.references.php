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

$references	= $this->getFieldData( 'references' , $post->params );
$targetRef	= $system->config->get('main_reference_link_new_window') ? ' target="_blank"' : '';
?>
<?php if( $references && $system->config->get( 'reply_field_references') ) { ?>
<div class="discuss-references mt-15">
	<p class="reference-title"><strong><?php echo JText::_( 'COM_EASYDISCUSS_REFERENCES' ); ?>:</strong></p>
	<ol class="reference-list">
		<?php foreach( $references as $reference ){ ?>
			<?php
			$reference	= strip_tags( $reference );

			if( JString::stristr( $reference, 'https://' ) === false && JString::stristr( $reference, 'http://' ) === false )
			{
				$reference	= 'http://' . $reference;
			}

			// Remove quotes
			$reference	= str_ireplace( array( '"' , "'" ) , '' , $reference );
			?>
			<li>
				<a href="<?php echo $this->escape( $reference ); ?>"<?php echo $targetRef; ?>><?php echo $this->escape( $reference ); ?></a>
			</li>
		<?php } ?>
	</ol>
</div>
<?php } ?>
