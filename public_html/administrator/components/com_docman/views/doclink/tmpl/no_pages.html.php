<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-empty-state">
    <h4 ><?= translate('No menu items found') ?></h4>
    <p><?= translate('Docman menu warning'); ?></p>
    <p><?= translate('Docman menu warning instruction'); ?></p>
    <? if ($admin): ?>
        <p><a href="<?= JRoute::_('index.php?option=com_menus&view=items'); ?>" target="_parent" class="k-button k-button--large k-button--success"><?= translate('Go to menu manager') ?></a></p>
    <? endif; ?>
</div>
