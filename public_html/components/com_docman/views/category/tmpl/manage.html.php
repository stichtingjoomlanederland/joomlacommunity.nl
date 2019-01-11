<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die;

$redirect     = isset($redirect) ? $redirect : 'referrer';
$show_delete  = isset($show_delete) ? $show_delete : $category->canPerform('delete');
$show_edit    = isset($show_edit) ? $show_edit : $category->canPerform('edit');
$button_size  = 'btn-'.(isset($button_size) ? $button_size : 'small');
$parentOpen   = isset($parent) ? '<' . $parent . (isset($parentClass) ? ' class="' . $parentClass . '"' : '') . '>' : '<p>';
$parentClose  = isset($parent) ? '</' . $parent . '>' : '</p>';

if ($redirect === 'referrer' && isset($menu) && isset($menu->query['slug']) && $menu->query['slug'] == $category->slug) {
    $show_delete = false;
}

if ($category->isNew()) {
    $show_delete = $show_edit = false;
}
?>

<? // Edit and delete buttons ?>
<? if (!($category->isLockable() && $category->isLocked()) && ($show_edit || $show_delete)): ?>
<?= $parentOpen; ?>
    <? // Edit ?>
    <? if ($show_edit): ?>
        <a class="btn btn-default <?= $button_size ?>"
           href="<?= helper('route.category', array(
               'entity' => $category,
               'view' => 'category',
               'layout' => 'form'
           )); ?>">
            <?= translate('Edit'); ?>
        </a>
    <? endif ?>
    <? // Delete ?>
    <? if ($show_delete):
    $data = array(
        'method' => 'post',
        'url' => (string) helper('route.category',array('entity' => $category), true, false),
        'params' => array(
            'csrf_token' => object('user')->getSession()->getToken(),
            '_method'    => 'delete',
            '_referrer'  => base64_encode((string) object('request')->getUrl())
        )
    );

    if ($redirect === 'referrer')
    {
        if ((string)object('request')->getReferrer()) {
            $data['params']['_referrer'] = base64_encode((string) object('request')->getReferrer());
        } else {
            $data['params']['_referrer'] = base64_encode(JURI::base());
        }
    }
    ?>
        <?= helper('behavior.deletable'); ?>
        <a class="btn <?= $button_size ?> btn-danger docman-deletable" href="#" rel="<?= escape(json_encode($data)) ?>">
          <?= translate('Delete') ?>
        </a>
    <? endif ?>
<?= $parentClose; ?>
<? endif ?>
