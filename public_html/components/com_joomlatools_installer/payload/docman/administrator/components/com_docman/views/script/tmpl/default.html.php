<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<?= helper('behavior.bootstrap', array('javascript' => true, 'css' => true)) ?>
<?= helper('ui.load', array('styles' => false)) ?>

<ktml:script src="media://com_docman/js/admin/script.js" />

<ktml:style src="media://koowa/com_migrator/css/migrator.css" />


<script type="text/javascript">

    if (typeof Docman === 'undefined') {
        Docman = {};
    }

    Docman.token    = '<?= $token; ?>';
    Docman.base_url = '<?= route('view=script&format=json&script='.$script, true, false); ?>';
    Docman.jobs = <?= json_encode($jobs) ?>;
</script>


<div class="k-migrator-container">
    <div class="migrator">
        <div class="migrator__header">
            <img class="joomlatools_logo" src="media://koowa/com_migrator/img/joomlatools_logo_80px.png" alt="Joomlatools logo" />
            <?= $title ?>
        </div>
        <div class="migrator__steps">
            <ul class="migrator__steps__list">
                <li class="migrator__steps__list__item"><?= translate('Running') ?></li>
                <li class="migrator__steps__list__item"><?= translate('Completed') ?></li>
            </ul>
        </div>
        <div class="migrator__wrapper migrator--step1">
            <h1><?= translate('Running') ?></h1>
            <div id="message-container" class="migrator__content"></div>
            <div class="migrator_alert">
                <p><?= translate('Do not close this page or use the back button during the process!') ?></p>
            </div>
        </div>
        <div class="migrator__wrapper migrator--step2" style="display: none">
            <h1><?= translate('Completed') ?></h1>
            <div class="migrator__content">
                <p><a class="migrator_button" href="<?= $go_back ?>">
                <?= translate('Go back') ?></a></p>
            </div>
        </div>
    </div>
</div>
