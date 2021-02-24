<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die;

$show_delete   = isset($show_delete) ? $show_delete : $document->canPerform('delete');
$show_edit     = isset($show_edit) ? $show_edit : $document->canPerform('edit');
$button_size   = 'btn-'.(isset($button_size) ? $button_size : 'small');
?>

<? // Edit and delete buttons ?>
<? if (!($document->isLockable() && $document->isLocked()) && ($show_edit || $show_delete)): ?>
<div class="btn-toolbar koowa_toolbar k-no-wrap">
    <? // Edit ?>
    <? if ($show_edit): ?>
        <a class="btn btn-default <?= $button_size ?>"
           href="<?= helper('route.document', array('entity' => $document, 'layout' => 'form'));?>"
        ><?= translate('Edit'); ?></a>
    <? endif ?>

    <? // Delete ?>
    <? if ($show_delete):
        $data = array(
            'method' => 'post',
            'url'    => (string)helper('route.document',array('entity' => $document), true, false),
            'params' => array(
                'csrf_token' => object('user')->getSession()->getToken(),
                '_method'    => 'delete',
                '_referrer'  => base64_encode((string) object('request')->getUrl())
            )
        );

        if (parameters()->view == 'document')
        {
            if ((string)object('request')->getReferrer()) {
                $data['params']['_referrer'] = base64_encode((string) object('request')->getReferrer());
            } else {
                $data['params']['_referrer'] = base64_encode(JURI::base());
            }
        }
    ?>
        <ktml:script src="media://com_docman/js/site/items.js" />

        <a class="btn <?= $button_size ?> btn-danger" data-action="delete-item" href="#" rel="<?= escape(json_encode($data)) ?>"
           <?= parameters()->view == 'document' || parameters()->layout === 'default' ? 'data-ajax="false"' : '' ?>
            data-url="<?= escape($data['url']) ?>"
            data-params="<?= escape(json_encode($data['params'])) ?>"
            data-prompt="<?= escape(translate('You will not be able to bring this item back if you delete it. Would you like to continue?')) ?>"
            data-document="<?= $document->uuid ?>"
            >
            <?= translate('Delete') ?>
        </a>
    <? endif ?>
</div>
<? endif ?>
