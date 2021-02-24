<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif] -->
<div style="background:#f9f9fa;background-color:#f9f9fa;margin:0px auto;max-width:480px;">
	<table role="presentation" style="background:#f9f9fa;background-color:#f9f9fa;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
		<tbody>
			<tr>
				<td style="direction:ltr;font-size:0px;padding:10px 0 10px;text-align:center;">
					<!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:440px;"><![endif]-->
					<div class="mj-column-px-440 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td style="background-color:#ffffff;border-radius:8px;vertical-align:top;padding:0;">
										<table role="presentation" style="" width="100%" cellspacing="0" cellpadding="0" border="0">
											<tbody>
												<tr>
													<td vertical-align="middle" style="font-size:0px;padding:0;word-break:break-word;" align="center">
														<table role="presentation" style="border-collapse:separate;width:440px;line-height:100%;" cellspacing="0" cellpadding="0" border="0">
															<tbody>
																<tr>
																	<td role="presentation" style="border:1px solid #ebebeb;border-radius:8px;cursor:auto;mso-padding-alt:10px 16px;text-align:left;background:#ffffff;" valign="middle" bgcolor="#ffffff" align="center">
																		<a href="<?php echo $attachment->getDownloadLink(); ?>" style="display: inline-block; width: 406px; background: #ffffff; color: #4e72e2; font-family: Ubuntu, Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 16px; margin: 0; text-transform: none; padding: 10px 16px; mso-padding-alt: 0px; border-radius: 8px; text-decoration: none;" target="_blank">
																			<img src="<?php echo rtrim(JURI::root(), '/');?>/media/com_easydiscuss/images/emails/email-attach-icon.jpg" style="width:18px; vertical-align:middle;"> &nbsp; <?php echo $attachment->title;?>
																		</a>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<!--[if mso | IE]></td></tr></table><![endif]-->
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--[if mso | IE]></td></tr></table><![endif]-->