<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<? // Loading necessary Markup, CSS and JS ?>
<?= helper('ui.load') ?>


<?= helper('behavior.keepalive'); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

                <!-- Component wrapper -->
                <div class="k-component-wrapper">

                    <!-- Component -->
                    <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                        <!-- Container -->
                        <div class="k-container">

                            <!-- Main information -->
                            <div class="k-container__main">

                                <fieldset class="k-form-block">

                                    <div class="k-form-block__header">
                                        <?= translate('Menu items'); ?>
                                    </div>

                                    <div class="k-form-block__content">

                                        <div class="k-form-group">
                                    <ul>
                                    <? foreach ($pages as $page): 
                                        $access = JTable::getInstance('Viewlevel');
                                        $access->load(['id' => $page->access]);?>
                                        <li>
                                            <a href="<?= JRoute::_('index.php?option=com_menus&task=item.edit&id='.$page->id) ?>"
                                               target="_blank"
                                               >
                                                <?= $page->title; ?><br />
                                            </a>
                                            <small>menu: <?= $page->menutype; ?></small><br />
                                            <small>link: <?= $page->link; ?></small><br />
                                            <small>access: <?= $access ? $access->title : $page->access; ?></small><br />
                                            <? if (!empty($page->language)): ?>
                                                <small>language: <?= $page->language; ?></small><br />
                                            <? endif ?>
                                        </li>
                                    <? endforeach; ?>
                                    </ul>
                                        </div>
                                    </div>

                                </fieldset>
                            </div><!-- .k-container__main -->

                            <!-- Other information -->
                            <div class="k-container__sub">

                                <fieldset class="k-form-block">

                                    <div class="k-form-block__header">
                                        <?= translate('General'); ?>
                                    </div>

                                    <div class="k-form-block__content">

                                        <div class="k-form-group">
                                            <ul>
                                                <li><a href="<?= route('view=config&layout=debug_phpinfo') ?>">PHP info</a></li>
                                                <li>Document count: <?= $document_count ?></li>
                                                <li>Category count: <?= $category_count ?></li>
                                                <li>Tag count: <?= $tag_count ?></li>
                                                <li>User count: <?= $user_count ?></li>
                                                <li>Folder count: <?= $folder_count ?></li>
                                                <li>File count: <?= $file_count ?></li>
                                                <li>Pending scan count: <?= $scan_count ?>
                                                    <small>
                                                        <a href="<?= route('view=scans&format=json') ?>" target="_blank">View scans</a>
                                                    </small>
                                                </li>
                                                <li><a href="<?= route('view=containers&format=json&routed=1') ?>" target="_blank">View file containers</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                </fieldset>

                                <fieldset class="k-form-block">

                                    <div class="k-form-block__header">
                                        <?= translate('Scheduler'); ?>
                                    </div>

                                    <div class="k-form-block__content">

                                        <div class="k-form-group">
                                            <p>
                                                <a class="k-button k-button--default"
                                                        href="<?= route('view=config&layout=debug_scheduler_log') ?>">Check Scheduler log</a>
                                            </p>
                                            <ul>
                                                <li>Last run: <?= $scheduler_metadata->last_run ?></li>
                                                <li>Sleep until: <?= $scheduler_metadata->sleep_until ?></li>
                                            </ul>
                                            <h4>Jobs</h4>
                                            <ul>
                                                <? foreach ($jobs as $job): ?>
                                                    <li>
                                                        <?= $job->id; ?> (<?= $job->frequency; ?>)<br />
                                                        <small>last run on: <?= $job->modified_on; ?></small><br />
                                                        <small>completed on: <?= $job->completed_on; ?></small><br />
                                                        <small>state:</small><br />
                                                        <pre><?= $job->state ?></pre>
                                                    </li>
                                                <? endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>

                                </fieldset>

                            </div><!-- .k-container__sub -->

                        </div><!-- .k-container -->

                    </form><!-- .k-component -->

                </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
