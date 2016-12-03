		<?php if( $recaptcha = $this->getRecaptcha() ){ ?>
		<hr />
		<div class="control-group">
			<div id="post_new_antispam"><?php echo $recaptcha; ?></div>
		</div>
		<?php }else if( DiscussHelper::getHelper( 'Captcha' )->showCaptcha() ){ ?>
			<?php echo DiscussHelper::getHelper( 'Captcha' )->getHTML();?>
		<?php } ?>