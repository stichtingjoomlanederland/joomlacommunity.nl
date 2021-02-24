<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-sidebar-left k-js-sidebar-left">

    <!-- Navigation -->
    <div class="k-sidebar-item">
        <ktml:toolbar type="menubar">
    </div>

    <!-- Filters -->
    <div class="k-sidebar-item k-js-sidebar-toggle-item">
        <div class="k-sidebar-item__header">
            <?= translate('Quick filters'); ?>
        </div>
        <ul class="k-list">
            <li class="<?= parameters()->created_by == object('user')->getId() ? 'k-is-active' : ''; ?>">
                <a href="<?= route('created_by='.(parameters()->created_by == 0 ? object('user')->getId() : '')) ?>">
                    <span class="k-icon-person" aria-hidden="true"></span>
                    <?= translate('My Tags') ?>
                </a>
            </li>
            <li class="<?= parameters()->sort === 'created_on' && parameters()->direction === 'desc' ? 'k-is-active' : ''; ?>">
                <a href="<?= route(parameters()->sort === 'created_on' && parameters()->direction === 'desc' ? 'sort=&direction=&created_by=' : 'sort=created_on&direction=desc&created_by=') ?>">
                    <span class="k-icon-clock" aria-hidden="true"></span>
                    <?= translate('Recently Added') ?>
                </a>
            </li>
            <li class="<?= parameters()->sort === 'modified_on' && parameters()->direction === 'desc' ? 'k-is-active' : ''; ?>">
                <a href="<?= route(parameters()->sort === 'modified_on' && parameters()->direction === 'desc' ? 'sort=&direction=&created_by=' : 'sort=modified_on&direction=desc&created_by=') ?>">
                    <span class="k-icon-pencil" aria-hidden="true"></span>
                    <?= translate('Recently Edited') ?>
                </a>
            </li>
        </ul>
    </div>

</div><!-- .k-sidebar-left -->
