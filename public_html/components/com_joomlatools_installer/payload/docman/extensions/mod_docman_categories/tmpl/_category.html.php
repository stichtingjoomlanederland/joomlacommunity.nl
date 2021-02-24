<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<p class="koowa_header koowa_header--margin">
    <span class="koowa_header__item koowa_header__item--image_container">
        <a href="<?= $category->link; ?>">
            <? if ($category->icon && $params->show_icon): ?>
                <?= import('com://site/docman.document.icon.html', array('icon' => $category->icon, 'class' => 'k-icon--size-default')) ?>
            <? endif ?>
        </a>
    </span>
    <span class="koowa_header__item">
        <span class="koowa_wrapped_content">
            <span class="whitespace_preserver">
                <a href="<?= $category->link; ?>"><?= escape($category->title);?></a>
            </span>
        </span>
    </span>
</p>