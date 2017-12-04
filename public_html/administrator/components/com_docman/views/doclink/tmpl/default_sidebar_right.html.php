<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-sidebar-right k-js-sidebar-right">

    <div class="k-sidebar-item">

        <div class="k-sidebar-item__header">
            <?= translate('Selected file info'); ?>
        </div>

        <form class="k-sidebar-item__content" id="properties" onkeypress="return event.keyCode !== 13;">

            <div class="k-form-group">
                <input type="hidden" id="url" value="" />
                <label for="caption"><?= translate('File name'); ?></label>
                <input type="text" id="caption" value="" class="k-form-control" />
            </div>

            <div class="kform-group">
                <button type="button" id="insert-image" class="k-button k-button--primary k-button--block"><?= translate('Go') ?></button>
            </div>

        </form>

    </div>

</div><!-- .k-sidebar-right -->
