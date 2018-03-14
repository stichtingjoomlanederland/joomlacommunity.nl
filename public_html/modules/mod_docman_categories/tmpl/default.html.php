<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>
<? if (parameters()->total): ?>

<?= helper('ui.load', array(
    'package' => 'docman',
    'wrapper' => false
)); ?>

<div class="k-ui-namespace">
    <div class="mod_docman mod_docman--categories <?= JFactory::getLanguage()->isRTL() ? ' k-ui-rtl' : 'k-ui-ltr' ?>">
    <?
    foreach ($categories as $category):
        $level      = $category->level;
        $next_level = $categories->hasNext() ? $categories->getInnerIterator()->current()->level : false;

        if ($level > $previous_level): // Start a new level ?>
        <ul <?= $params->show_icon ? ' class="mod_docman_icons"' :'' ?>>
        <? endif; ?>
            <li class="module_document module_document__level<?= $level ?>">
            <?= import('mod://site/docman_categories._category.html', array(
                'category' => $category,
                'params'   => $params
            )); ?>
        <? if ($next_level === false && $level >= $next_level): ?>
            </li>
        <? endif; ?>

        <? if ($next_level === false || $level > $next_level): // Last one of the level ?>
            <? for($i = 0; $i < $level - $next_level; ++$i): ?>
                </ul>
                <? if ($next_level !== false): ?>
                </li>
                <? endif;
            endfor;
        endif;

    $previous_level = $level;
    endforeach; ?>

    </div>
</div>
<? endif; ?>
