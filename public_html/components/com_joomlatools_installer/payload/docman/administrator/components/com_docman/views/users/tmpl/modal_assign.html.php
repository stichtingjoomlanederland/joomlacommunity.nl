<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-ui-namespace k-small-inline-modal-holder mfp-hide js-usergroup-modal-assign">
    <div class="k-inline-modal">
        <form>
            <h3>
                <?= translate('Assign to') ?>
            </h3>
            <div class="k-form-group">
                <?= helper('listbox.groups', array(
                    'deselect' => true,
                    'check_access' => true,
                    'attribs' => array(
                        'class' => 'js-usergroup-groups',
                        'multiple' => true
                    )
                )) ?>
            </div>
            <button class="js-usergroup-action k-button k-button--primary" disabled >
                <?= translate('Assign'); ?>
            </button>
        </form>
    </div>
</div>

<div class="k-ui-namespace k-small-inline-modal-holder mfp-hide js-usergroup-modal-remove">
    <div class="k-inline-modal">
        <form>
            <h3>
                <?= translate('Remove from') ?>
            </h3>
            <div class="k-form-group">
                <?= helper('listbox.groups', array(
                    'deselect' => true,
                    'check_access' => true,
                    'attribs' => array(
                        'class' => 'js-usergroup-groups',
                        'multiple' => true
                    )
                )) ?>
            </div>
            <button class="js-usergroup-action k-button k-button--primary" disabled >
                <?= translate('Remove'); ?>
            </button>
        </form>
    </div>
</div>
