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
<?php if( $badges && $system->config->get('main_badges') ){ ?>
<ul class="discuss-badges reset-ul">
	<?php foreach( $badges as $badge ){ ?>
	<li>
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" >
			<img src="<?php echo $badge->getAvatar();?>" width="32" data-placement="top" rel="ed-tooltip" data-original-title="<?php echo $this->escape( $badge->get( 'title' ) );?>" />
		</a>
	</li>
	<?php } ?>
</ul>
<?php } ?>