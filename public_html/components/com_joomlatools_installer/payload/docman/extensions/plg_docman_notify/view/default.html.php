<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= translate(sprintf('PLG_DOCMAN_NOTIFY_DOCUMENT_%s_BODY', $action), array(
    'name'     => $name,
    'title'    => $title,
    'sitename' => $sitename
))?>

<? if (!empty($url)): ?>
    <?= translate('PLG_DOCMAN_NOTIFY_URL', array('url' => $url)) ?>
<? endif ?>
