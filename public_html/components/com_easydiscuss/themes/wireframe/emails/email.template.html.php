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
    <base href="&lt;?php echo JURI::root();?&gt;" target="_blank"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;">
    <table style="width:100%;background:#f4f4f4;margin:0;padding:50px 0 80px;color:#798796;font-family:'Lucida Grande',Tahoma,Arial;font-size:11px;">
        <tbody>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0" border="0" style="width:600px;table-layout:fixed;margin:0 auto;background:#fff;border:1px solid #ededed;border-top-color:#f4f4f4;border-bottom-color:#f4f4f4;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;">
                        <tbody>
                            <tr>
                                <td style="padding-top:20px;padding-left:20px;">
                                    <img src="<?php echo $logo;?>" />
                                </td>
                            </tr>

							<?php echo $contents; ?>

							<?php if( !empty( $replyBreakText ) ){ ?>
							<tr>
								<td style="line-height:1.5;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:center">
                                    <div style="font-size:11px; color:#999; line-height:13px;padding: 20px">

										<?php echo $replyBreakText; ?>
									</div>

								</td>
							</tr>
							<?php } ?>

                            <tr>
                                <td>
                                <br /><br />
                                    <table align="center" width="540" style="clear:both;margin:auto 20px">
                                        <tr>
                                            <td style="line-height:1.5;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:center">

												<?php if( !empty( $unsubscribeLink ) ){ ?>
                                                <div style="font-size:11px; color:#999; line-height:13px;padding: 20px">

													<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_SUBSCRIPTION_STATEMENT' ); ?><br />
													<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_TO_UNSUBSCRIBE' );?> <a href="<?php echo $unsubscribeLink;?>" style="color:#477fda"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CLICK_HERE' );?></a>.  <a href="<?php echo $subscriptionsLink; ?>" style="color:#477fda;"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_MANAGE_SUBSCRIPTIONS' ); ?></a>
												</div>
												<?php } else { ?>
													<br /><br />
												<?php } ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
