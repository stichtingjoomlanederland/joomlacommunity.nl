<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<fieldset class="k-form-block">

    <div class="k-form-block__header">
        <?= translate('Featured image') ?>
    </div>

    <div class="k-form-block__content">
        <div class="k-form-group">
            <?= helper('behavior.thumbnail', array(
                'entity' => $document
            )) ?>
        </div>
    </div>

</fieldset>