<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('ui.load') ?>


<?= helper('behavior.keepalive'); ?>
<?= helper('behavior.validator'); ?>


<!-- Wrapper -->
<div class="k-wrapper k-js-wrapper">

    <!-- Overview -->
    <div class="k-content-wrapper">

        <!-- Content -->
        <div class="k-content k-js-content">

            <!-- Toolbar -->
            <ktml:toolbar type="actionbar">

            <!-- Component wrapper -->
            <div class="k-component-wrapper">

                <!-- Component -->
                <form class="k-component k-js-component k-js-form-controller" action="" method="post">

                    <!-- Container -->
                    <div class="k-container">

                        <!-- Main information -->
                        <div class="k-container__main">

                            <fieldset>

                                <div class="k-form-group k-form-group--large">
                                    <input required
                                           class="k-form-control"
                                           id="docman_form_title"
                                           type="text"
                                           name="title"
                                           maxlength="255"
                                           placeholder="<?= translate('Title') ?>"
                                           value="<?= escape($tag->title); ?>" />
                                </div>

                                <div class="k-form-group">
                                    <div class="k-input-group k-input-group--small">
                                        <label for="todo_form_alias" class="k-input-group__addon">
                                            <?= translate('Alias') ?>
                                        </label>
                                        <input id="docman_form_alias"
                                               type="text"
                                               class="k-form-control"
                                               name="slug"
                                               maxlength="255"
                                               value="<?= escape($tag->slug) ?>"
                                               placeholder="<?= translate('Will be created automatically') ?>" />
                                    </div>
                                </div>

                            </fieldset>

                        </div><!-- .k-container__main -->

                    </div><!-- .k-container -->

                </form><!-- .k-component -->

            </div><!-- .k-component-wrapper -->

        </div><!-- .k-content -->

    </div><!-- .k-content-wrapper -->

</div><!-- .k-wrapper -->
