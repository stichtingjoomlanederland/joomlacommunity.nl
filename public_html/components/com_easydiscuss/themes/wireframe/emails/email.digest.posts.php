<?php if ($site && $site->posts) { ?>

	<?php echo $this->html('email.spacer'); ?>

	<?php echo $this->html('email.sectionHeading', 'COM_EASYDISCUSS_DIGEST_SITE_UPDATES', 'COM_EASYDISCUSS_DIGEST_NEW_DISCUSSIONS_POSTED'); ?>

	<?php echo $this->html('email.spacer'); ?>


	<?php foreach ($site->posts as $post) { ?>
	<!--[if mso | IE]>
	<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
	<![endif]-->
	<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
		<table role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
		<tbody>
		<tr>
			<td style="direction:ltr;font-size:0px;padding:10px 20px;text-align:center;">
				<!--[if mso | IE]>
				<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:440px;">
				<![endif]-->
				<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
					<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
					<tr>
						<td style="vertical-align:top;padding:0;">
							<table role="presentation" style="" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
							<tr>
								<td style="font-size:0px;padding:0;padding-top:10px;padding-bottom:4px;word-break:break-word;" align="left">
									<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;font-weight:bold;line-height:22px;text-align:left;color:#444444;">
										<a href="<?php echo $post->getPermalink(true); ?>" style="color: #4e72e2; text-decoration: none;"><?php echo $post->getTitle(); ?></a>
									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:0px;padding:0;padding-bottom:16px;word-break:break-word;" align="left">
									<div style="font-family:'Roboto', Arial, sans-serif;font-size:13px;line-height:14px;text-align:left;color:#aaaaaa;">
										<?php echo JText::_('COM_EASYDISCUSS_DIGEST_BY'); ?> <?php echo ($post->user_id) ? $post->getOwner()->getName() : $post->poster_name; ?>
									</div>
								</td>
							</tr>
							<tr>
								<td style="font-size:0px;padding:0;word-break:break-word;" align="left">
									<div style="font-family:'Roboto', Arial, sans-serif;font-size:14px;line-height:18px;text-align:left;color:#888888;">
										<?php echo $post->getIntro(); ?>
									</div>
								</td>
							</tr>
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
	<!-- [if mso | IE]>
	</td></tr></table>
	<![endif]-->
	<?php } ?>
<?php } ?>

<?php if ($cats) { ?>
	<?php echo $this->html('email.spacer');?>

	<?php foreach($cats as $cat) { ?>
		<?php if ($cat->posts) { ?>
			<?php echo $this->html('email.spacer'); ?>

			<?php echo $this->html('email.sectionHeading', $cat->title, JText::sprintf('COM_EASYDISCUSS_DIGEST_NEW_DISCUSSIONS_POSTED_IN_CATEGORY', $cat->title)); ?>

			<?php echo $this->html('email.spacer'); ?>

			<?php foreach ($cat->posts as $post) { ?>
			<!--[if mso | IE]>
			<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
			<![endif]-->
			<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
				<table role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;" cellspacing="0" cellpadding="0" border="0" align="center">
				<tbody>
				<tr>
					<td style="direction:ltr;font-size:0px;padding:10px 20px;text-align:center;">
						<!--[if mso | IE]>
						<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top;width:440px;">
						<![endif]-->
						<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
							<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
							<tr>
								<td style="vertical-align:top;padding:0;">
									<table role="presentation" style="" width="100%" cellspacing="0" cellpadding="0" border="0">
									<tbody>
									<tr>
										<td style="font-size:0px;padding:0;padding-top:10px;padding-bottom:4px;word-break:break-word;" align="left">
											<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;font-weight:bold;line-height:22px;text-align:left;color:#444444;">
												<a href="<?php echo $post->getPermalink(true); ?>" style="color: #4e72e2; text-decoration: none;"><?php echo $post->getTitle(); ?></a>
											</div>
										</td>
									</tr>
									<tr>
										<td style="font-size:0px;padding:0;padding-bottom:16px;word-break:break-word;" align="left">
											<div style="font-family:'Roboto', Arial, sans-serif;font-size:13px;line-height:14px;text-align:left;color:#aaaaaa;">
												<?php echo JText::_('COM_EASYDISCUSS_DIGEST_BY'); ?> <?php echo ($post->user_id) ? $post->getOwner()->getName() : $post->poster_name; ?>
											</div>
										</td>
									</tr>
									<tr>
										<td style="font-size:0px;padding:0;word-break:break-word;" align="left">
											<div style="font-family:'Roboto', Arial, sans-serif;font-size:14px;line-height:18px;text-align:left;color:#888888;">
												<?php echo $post->getIntro(); ?>
											</div>
										</td>
									</tr>
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
			<!-- [if mso | IE]>
			</td></tr></table>
			<![endif]-->
			<?php } ?>

			<?php echo $this->html('email.spacer'); ?>
		<?php } ?>
	<?php } ?>
<?php } ?>
