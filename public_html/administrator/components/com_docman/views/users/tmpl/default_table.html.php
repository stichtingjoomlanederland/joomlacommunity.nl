<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<div class="k-table-container">
    <div class="k-table">

        <table class="k-js-responsive-table">
            <thead>
                <tr>
                    <th width="1%" class="k-table-data--form">
                        <?= helper('grid.checkall')?>
                    </th>
                    <th width="1%" class="k-table-data--toggle" data-toggle="true"></th>
                    <th>
                        <?= helper('grid.sort', array('column' => 'name', 'title' => 'Name')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                        <?= helper('grid.sort', array('column' => 'username', 'title' => 'Username')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                        <?= helper('grid.sort', array('column' => 'block', 'title' => 'Enabled')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                        <?= helper('grid.sort', array('column' => 'activation', 'title' => 'Activated')); ?>
                    </th>
                    <th width="10%" data-hide="phone,tablet,desktop">
                        <?= translate('User Groups') ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                        <?= helper('grid.sort', array('column' => 'lastvisitDate', 'title' => 'Last Visit Date')); ?>
                    </th>
                    <th width="5%" data-hide="phone,tablet,desktop">
                        <?= helper('grid.sort', array('column' => 'registerDate', 'title' => 'Registration Date')); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <? foreach($users as $user): ?>
                <tr>
                    <td class="k-table-data--form">
                        <?= helper('grid.checkbox', array('entity' => $user))?>
                    </td>
                    <td class="k-table-data--toggle"></td>
                    <td class="k-table-data--ellipsis">
                        <?= escape($user->name) ?>
                    </td>
                    <td class="k-table-data--ellipsis">
                        <?= $user->username ?>
                    </td>
                    <td>
                        <span class="k-icon-<?= ($user->block) ? 'x' : 'check' ?> k-icon--<?= ($user->block) ? 'error' : 'success' ?>"></span>
                    </td>
                    <td>
                        <span class="k-icon-<?= ($user->activation) ? 'x' : 'check' ?> k-icon--<?= ($user->block) ? 'error' : 'success' ?>"></span>
                    </td>
                    <td class="k-table-data--ellipsis">
                        <small
                           data-k-tooltip='{"container":".k-ui-container","delay":{"show":50,"hide":50}}'
                           data-original-title="<?= implode('<br /> ', helper('user.groups', array('user_id' => $user->id))) ?>">
                            <?= implode(', ', helper('user.groups', array('user_id' => $user->id))) ?>
                        </small>
                    </td>
                    <td class="k-table-data--nowrap">
                        <? if ($user->lastvisitDate === '0000-00-00 00:00:00'): ?>
                            -
                        <? else: ?>
                            <?= helper('date.format', array('date' => $user->lastvisitDate)); ?>
                        <? endif ?>
                    </td>
                    <td class="k-table-data--nowrap">
                        <?= helper('date.format', array('date' => $user->registerDate)); ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </div><!-- .k-table -->

    <? if (count($users)): ?>
        <div class="k-table-pagination">
            <?= helper('paginator.pagination') ?>
        </div><!-- .k-table-pagination -->
    <? endif; ?>

</div><!-- .k-table-container -->
