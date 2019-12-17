<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="acym_front_page">

    <?php
    if (!empty($data['paramsJoomla']['show_page_heading'])) {
        echo '<h1 class="contentheading'.$data['paramsJoomla']['suffix'].'">'.$data['paramsJoomla']['page_heading'].'</h1>';
    }
    ?>

	<div class="acym__front__archive">
		<form method="post" action="<?php echo acym_completeLink('archive'); ?>" id="acym_form">
			<h1 class="acym__front__archive__title"><?php echo acym_translation('ACYM_NEWSLETTERS'); ?></h1>

            <?php
            foreach ($data['newsletters'] as $oneNewsletter) {
                $archiveURL = acym_frontendLink('archive&task=view&id='.$oneNewsletter->id.'&'.acym_noTemplate());

                if ($data['popup']) {
                    ?>
					<p class="acym__front__archive__newsletter_name" data-nlid="<?php echo $oneNewsletter->id; ?>"><?php echo $oneNewsletter->subject; ?></p>

					<div id="acym__front__archive__modal__<?php echo $oneNewsletter->id; ?>" class="acym__front__archive__modal" style="display: none;">
						<div class="acym__front__archive__modal__content">
							<div class="acym__front__archive__modal__close"><span>&times;</span></div>

                            <?php
                            if (empty($data['userId'])) echo '<p class="acym_front_message_warning">'.acym_translation('ACYM_FRONT_ARCHIVE_NOT_CONNECTED').'</p>';

                            $iframeClass = 'acym__front__archive__modal__iframe';
                            if (empty($data['userId'])) $iframeClass .= ' acym__front__not_connected_user';
                            ?>

							<iframe class="<?php echo $iframeClass; ?>" src="<?php echo $archiveURL; ?>"></iframe>
						</div>
					</div>
                    <?php
                } else {
                    echo '<p class="acym__front__archive__newsletter_name"><a href="'.$archiveURL.'" target="_blank">'.$oneNewsletter->subject.'</a></p>';
                }
                echo '<p class="acym__front__archive__newsletter_sending-date">'.acym_translation('ACYM_SENDING_DATE').' : '.acym_date($oneNewsletter->sending_date, 'd M Y').'</p>';
            }

            echo $data['pagination']->displayFront();
            ?>

			<input type="hidden" name="page" id="acym__front__archive__next-page">
		</form>
	</div>
</div>

