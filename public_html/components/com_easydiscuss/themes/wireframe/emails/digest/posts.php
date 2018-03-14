<?php if ($site && $site->posts) { ?>

<!-- Start Container -->
<table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="container">
    <tr>
        <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
    </tr>
    <tr>
        <td class="mobile" style="font-family:arial, sans-serif; font-size:18px; line-height:32px; font-weight:bold;">
            <?php echo JText::_('COM_EASYDISCUSS_DIGEST_SITE_UPDATES'); ?>
        </td>
    </tr>
    <tr>
        <td class="mobile" style="font-family:arial, sans-serif; font-size:16px; line-height:26px;color:#888">
            <?php echo JText::_('COM_EASYDISCUSS_DIGEST_NEW_DISCUSSIONS_POSTED'); ?>
        </td>
    </tr>
    <tr>
        <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
    </tr>

    <?php foreach ($site->posts as $post) { ?>
        <!-- Start Link -->
        <tr>
            <td style="font-family:Verdana, Arial, sans serif; font-size: 14px; color: #4d4d4d; line-height:18px;">
                <a href="<?php echo $post->getPermalink(true); ?>" target="_blank" alias="" style="color: #458BC6; text-decoration: none;"><?php echo $post->getTitle(); ?></a>
            </td>
        </tr>
        <tr>
            <td style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #888; line-height:16px;">
                <?php echo JText::_('COM_EASYDISCUSS_DIGEST_BY'); ?> <?php echo ($post->user_id) ? $post->getOwner()->getName() : $post->poster_name; ?> (<?php echo ($post->user_id) ? $post->getOwner()->getEmail() : $post->poster_email; ?>)
            </td>
        </tr>
        <tr>
            <td style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #888; line-height:16px;">
                <?php echo $post->getIntro(); ?>
            </td>
        </tr>
        <tr>
            <td height="15" style="line-height:10px; font-size:10px;"> </td><!-- Spacer -->
        </tr>
        <!-- End Link -->
    <?php } ?>

        <tr>
            <td height="10" style="line-height:10px; font-size:10px;"> </td><!-- Spacer -->
        </tr>

    <tr>
        <td height="20" style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #4d4d4d; line-height:18px;">

                <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_SUBSCRIPTION_STATEMENT' ); ?><br />
                <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_TO_UNSUBSCRIBE' );?>
                <a style="font-size:12px; line-height:18px; color:#888; text-decoration:underline;" alias="" target="_blank" href="<?php echo $site->unlink;?>">
                    <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CLICK_HERE' );?>
                </a>.

        </td>
    </tr>

    <tr>
        <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
    </tr>
</table>

<?php } ?>


<!-- Start Divider Decor -->
<table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="container" bgcolor="#eee">
    <tr>
        <td>
            <img style="min-width:560px; display:block; margin:0; padding:0" class="mobileOff" width="560" height="1" src="<?php echo rtrim(JURI::root(), '/'); ?>/media/com_easydiscuss/images/spacer.gif"/>
        </td>
    </tr>
</table>
<!-- End Divider Decor -->



<?php if ($cats) { ?>
    <?php foreach($cats as $cat) { ?>
        <?php if ($cat->posts) { ?>


        <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="container">
            <tr>
                <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
            </tr>
            <tr>
                <td class="mobile" style="font-family:arial, sans-serif; font-size:18px; line-height:32px; font-weight:bold;">
                    <?php echo $cat->title; ?>
                </td>
            </tr>
            <tr>
                <td class="mobile" style="font-family:arial, sans-serif; font-size:16px; line-height:26px;color:#888">
                    <?php echo JText::sprintf('COM_EASYDISCUSS_DIGEST_NEW_DISCUSSIONS_POSTED_IN_CATEGORY', $cat->title); ?>
                </td>
            </tr>
            <tr>
                <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
            </tr>

            <?php foreach ($cat->posts as $post) { ?>
                <!-- Start Link -->
                <tr>
                    <td style="font-family:Verdana, Arial, sans serif; font-size: 14px; color: #4d4d4d; line-height:18px;">
                        <a href="<?php echo $post->getPermalink(true); ?>" target="_blank" alias="" style="color: #458BC6; text-decoration: none;">
                            <?php echo $post->getTitle(); ?>
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #888; line-height:16px;">
                        <?php echo JText::_('COM_EASYDISCUSS_DIGEST_BY'); ?> <?php echo ($post->user_id) ? $post->getOwner()->getName() : $post->poster_name; ?> (<?php echo ($post->user_id) ? $post->getOwner()->getEmail() : $post->poster_email; ?>)
                    </td>
                </tr>
                <tr>
                    <td style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #888; line-height:16px;">
                        <?php echo $post->getIntro(); ?>
                    </td>
                </tr>
                <tr>
                    <td height="15" style="line-height:10px; font-size:10px;"> </td><!-- Spacer -->
                </tr>

                <!-- End Link -->
            <?php } ?>

            <tr>
                <td height="10" style="line-height:10px; font-size:10px;"> </td><!-- Spacer -->
            </tr>

            <tr>
                <td height="20" style="font-family:Verdana, Arial, sans serif; font-size: 12px; color: #4d4d4d; line-height:18px;">

                        <?php echo JText::sprintf( 'COM_EASYDISCUSS_DIGEST_CATEGORY_SUBSCRIPTION_STATEMENT', $cat->title); ?>
                        <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_TO_UNSUBSCRIBE' );?>
                        <a style="font-size:12px; line-height:18px; color:#888; text-decoration:underline;" alias="" target="_blank" href="<?php echo $cat->unlink;?>">
                            <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CLICK_HERE' );?>
                        </a>.

                </td>
            </tr>

            <tr>
                <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
            </tr>

            </table>

            <!-- Start Divider Decor -->
            <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="container" bgcolor="#eee">
                <tr>
                    <td>
                        <img style="min-width:560px; display:block; margin:0; padding:0" class="mobileOff" width="560" height="1" src="<?php echo rtrim(JURI::root(), '/'); ?>/media/com_easydiscuss/images/spacer.gif"/>
                    </td>
                </tr>
            </table>
            <!-- End Divider Decor -->

        <?php } ?>
    <?php } ?>
<?php } ?>
