<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= helper('ui.load'); ?>


<? // Page header ?>
<? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= escape($params->get('page_heading')); ?>
    </h1>
<? endif; ?>


<? // Header ?>
<h2><?= translate('Thank you for your submission.'); ?></h2>


<? // Message ?>
<? if (!$params->auto_publish): ?>
    <p>
        <?= translate('Your submission will be reviewed first before getting published.'); ?>
    </p>
<? endif; ?>