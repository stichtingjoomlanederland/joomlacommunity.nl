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
<?php echo JText::sprintf( 'COM_EASYDISCUSS_EMAILTEMPLATE_TOTAL_OVER_INTERVAL' , $total , $interval );?>
<br />
<hr style="clear:both;margin:10px 0 15px;padding:0;border:0;border-top:1px solid #ddd" />
<?php foreach($post as $data){ ?>
<div style="display:inline-block;width:100%;padding-bottom:15px;margin-bottom:15px;border-bottom:1px solid #ddd">
	<img src="<?php echo $data['avatar'];?>" width="50" alt="<?php echo $data['name'];?>" style="float:left;width:50px;height:auto;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;" />
	<div style="margin-left:60px">
		<a href="<?php echo $data['link'];?>" style="font-weight:bold;color:#477fda;line-height:17px;display:inline-block;text-decoration:none"><?php echo $data['title'];?></a>
		<br>
		<span style="font-size:11px;color:#888;margin-top:3px;display:inline-block">
			<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_BY' );?> <a href="<?php echo $data['userlink'];?>" style="color:#888"><?php echo $data['name'];?></a> - <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_POSTED_IN' );?>
			<a href="<?php echo $data['categorylink'];?>" style="color:#888"><?php echo $data['category'];?></a> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_ON' );?> <?php echo $data['date'];?>.
		</span>
	</div>
</div>
<?php } ?>
