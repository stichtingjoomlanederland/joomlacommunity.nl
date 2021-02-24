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
<table role="presentation" style="background:#F2F4FA;background-color:#F2F4FA;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
<tbody>
<tr>
	<td>
		<!--[if mso | IE]>
			<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
		<![endif]-->

		<div style="margin:0px auto;max-width:480px;">
			<table role="presentation" style="width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
				<tbody>
				<tr>
  					<td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
						<!--[if mso | IE]>
						  <table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:480px;">
						<![endif]-->

						<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
							<tr>
								<td style="vertical-align:top;padding:0;padding-top:40px;padding-bottom:40px;">
									<table role="presentation" style="" width="100%" cellspacing="0" cellpadding="0" border="0">
									<tbody>
									<tr>
										<td style="font-size:0px;padding:0;padding-bottom:20px;word-break:break-word;" align="center">
											<div style="font-family:'Roboto', Arial, sans-serif;font-size:36px;font-weight:bold;line-height:52px;text-align:center;color:#444444;">
												<?php echo JText::_($title); ?>
											</div>
										</td>
									</tr>
									<?php if ($subtitle) { ?>
									<tr>
										<td style="font-size:0px;padding:0;padding-bottom:20px;word-break:break-word;" align="center">
											<div style="font-family:'Roboto', Arial, sans-serif;font-size:18px;line-height:30px;text-align:center;color:#444444;">
												<?php echo $subtitle;?>
												<!-- <b>James</b> created a new discussion <a href="#" style="color: #4e72e2; text-decoration: none;">Post title</a> in <a href="#" style="color: #4e72e2; text-decoration: none;">Category Title</a> -->
											</div>
										</td>
									</tr>
									<?php } ?>
									</tbody>
									</table>
								</td>
							</tr>
							</tbody>
							</table>
						</div>
						<!--[if mso | IE]>
							</td></tr></table>
						<![endif]-->
					</td>
				</tr>
				</tbody>
			</table>
		</div>

		<!--[if mso | IE]>
			</td></tr></table>
		<![endif]-->
	</td>
</tr>
</tbody>
</table>