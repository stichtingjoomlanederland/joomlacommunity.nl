<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php echo $this->html('email.heading', 'COM_EASYDISCUSS_EMAILS_NEW_DISCUSSION_POSTED', JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_YOU_CREATED_NEW_DISCUSSION', $postTitle, $postCategory)); ?>

<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">		
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:40px 20px 0;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:480px;">
			<![endif]-->

			<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
				<tbody>
				<tr>
					<td style="background-color:#ffffff;vertical-align:top;padding:0;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
						<tr>
							<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
	  							<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;line-height:30px;text-align:left;color:#444444;">
	  								<p><?php echo JText::_('COM_EASYDISCUSS_EMAILS_HELLO'); ?></p>
									<p><?php echo $emailIntro; ?></p>
								</div>
			  				</td>
						</tr>
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

<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->
<div style="background:#f9f9fa;background-color:#f9f9fa;margin:0px auto;max-width:480px;">	
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#f9f9fa;background-color:#f9f9fa;width:100%;">
	<tbody>
	<tr>
		<td style="direction:ltr;font-size:0px;padding:20px;text-align:center;">
			<!--[if mso | IE]>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:440px;">
			<![endif]-->
			
			<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
				<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
				<tbody>
				<tr>
					<td style="vertical-align:top;padding:0;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
						<tr>
							<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
								<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;line-height:22px;text-align:left;color:#888888;">
									<p>
										<?php echo $postContent; ?>
									</p>
								</div>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				</tbody>
				</table>
			</div>

			<?php if ($attachments) { ?>
				<?php foreach ($attachments as $attachment) { ?>
					<?php echo $this->html('email.attachment', $attachment); ?>
				<?php } ?>
			<?php } ?>

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

<?php echo $this->html('email.button', 'COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_DISCUSSION', $postLink); ?>