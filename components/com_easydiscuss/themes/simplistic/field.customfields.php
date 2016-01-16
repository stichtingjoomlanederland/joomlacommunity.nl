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
<div class="field-tag">
	<div class="input-wrap mr-10 mt-10">
		<?php
			$items = $post->getMyCustomFields( $post->id, DISCUSS_CUSTOMFIELDS_ACL_INPUT );

			if( isset($post->sessiondata) && $post->sessiondata )
			{
				$items = $post->mapCustomFieldsSession( $items );
			}

			if( $this instanceof DiscussThemes )
			{
				echo $this->loadTemplate( 'field.customfields.form.php' , array( 'items' => $items ) );
			}
			elseif ( isset($theme) && ($theme instanceof DiscussThemes) )
			{
				echo $theme->loadTemplate( 'field.customfields.form.php' , array( 'items' => $items ) );
			}
		?>
	</div>
</div>
