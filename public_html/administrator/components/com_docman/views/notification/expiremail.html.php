<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= translate('DOCMAN_EXPIRE_MAIL_BODY', array(
    'title'    => $title,
    'url'      => $url,
    'sitename' => $sitename
))?>