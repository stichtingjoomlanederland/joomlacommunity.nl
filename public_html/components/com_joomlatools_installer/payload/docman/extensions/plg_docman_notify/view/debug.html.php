<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<p><?= translate('PLG_DOCMAN_NOTIFY_DEBUG_EMAIL_BODY') ?></p>

<hr>

<?foreach ($notifications as $status => $status_notifications): ?>
    <?foreach ($status_notifications as $title => $recipients): ?>
        <p><?= renderNotification($title, $status, $recipients) ?></p>
        <p><?= translate('PLG_DOCMAN_NOTIFY_DEBUG_EMAIL_RECIPIENTS', array('recipients' => implode(', ', $recipients))) ?></p>
        <hr>
    <? endforeach ?>
<? endforeach ?>