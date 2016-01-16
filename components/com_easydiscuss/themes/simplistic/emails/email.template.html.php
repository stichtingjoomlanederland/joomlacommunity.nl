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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="<?php echo DISCUSS_SITE_THEMES_URI;?>/<?php echo strtolower( $system->config->get( 'layout_site_theme', 'simplistic' ) ); ?>/styles/style.css" type="text/css" />
</head>

<body style="margin:0;padding:0">
<div style="width:100%;background:#ddd;margin:0;padding:50px 0 80px;color:#798796;font-family:'Lucida Grande',Tahoma,Arial;font-size:11px;">
	<center>
		<table cellpadding="0" cellspacing="0" border="0" style="width:720px;background:#fff;border:1px solid #b5bbc1;border-bottom-color:#9ba3ab;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;">
			<tbody>
				<tr>
					<td style="padding:20px;border-bottom:1px solid #b5bbc1;;background:#f5f5f5;border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;-webkit-border-radius:3px 3px 0 0;">
						<b style="font-family:Arial;font-size:17px;font-weight:bold;color:#333;display:inline-block;"><?php echo $emailTitle;?></b>
					</td>
				</tr>
				<tr>
					<td style="padding:15px 20px;line-height:19px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:left">
						<?php echo $contents; ?>
					</td>
				</tr>
			</tbody>
		</table>

		<?php if( !empty( $replyBreakText ) ){ ?>
		<p style="padding:5px 20px;line-height:18px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:14px;text-align:center;font-weight:bold;margin:0">
			<?php echo $replyBreakText; ?>
		</p>
		<?php } ?>

		<?php if( !empty( $unsubscribeLink ) ){ ?>
		<p>
			<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_SUBSCRIPTION_STATEMENT' ); ?><br />
			<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_TO_UNSUBSCRIBE' );?> <a href="<?php echo $unsubscribeLink;?>" style="color:#477fda"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CLICK_HERE' );?></a>.  <a href="<?php echo $subscriptionsLink; ?>" style="color:#477fda;"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_MANAGE_SUBSCRIPTIONS' ); ?></a>
		</p>
		<?php } ?>
	</center>
</div>
</body>
</html>
