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
            <li class="<?= parameters()->sort === 'registerDate' && parameters()->direction === 'desc' ? 'k-is-active' : ''; ?>">
                <a href="<?= route(parameters()->sort === 'registerDate' && parameters()->direction === 'desc' ? 'sort=&direction=' : 'sort=registerDate&direction=desc') ?>">
                    <span class="k-icon-person" aria-hidden="true"></span>
                    <?= translate('Recently registered') ?>
                </a>
            </li>
            <li class="<?= parameters()->sort === 'lastvisitDate' && parameters()->direction === 'desc' ? 'k-is-active' : ''; ?>">
                <a href="<?= route(parameters()->sort === 'lastvisitDate' && parameters()->direction === 'desc' ? 'sort=&direction=' : 'sort=lastvisitDate&direction=desc') ?>">
                    <span class="k-icon-key" aria-hidden="true"></span>
                    <?= translate('Recently logged in') ?>
                </a>
            </li>
        </ul>
    </div>

</div><!-- .k-sidebar-left -->
