<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php foreach( $this->badges as $badge ){ ?>
<li rel="ed-tooltip" data-original-title="<?php echo $badge->custom ? $badge->custom : strip_tags( $badge->description ); ?>">
	<img src="<?php echo $badge->getAvatar(); ?>" width="48"  />

		<b><?php echo $badge->get( 'title' ); ?></b>

		<a href="javascript:void(0);" class="btn btn-danger btn-remove btn-mini removeBadge" data-id="<?php echo $badge->id;?>">
			<i></i>
		</a>

		<a href="javascript:void(0);" class="btn btn-mini btn-primary btn-addcustom addCustomMessage" data-id="<?php echo $badge->reference_id; ?>"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_CUSTOM_MESSAGE_BUTTON' );?></a>

</li>
<?php } ?>
