<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<div class="k-form-group">
    <div class="k-input-group k-input-group--large">
        <?= helper('behavior.icon', array(
            'name'  => 'parameters[icon]',
            'id'    => 'params_icon',
            'value' => $document->getParameters()->get('icon', 'default'),
            'link'  => route('option=com_docman&view=files&layout=select&container=docman-icons&types[]=image')
        ))?>
        <input required
               class="k-form-control"
               id="docman_form_title"
               type="text"
               name="title"
               maxlength="255"
               placeholder="<?= translate('Title') ?>"
               value="<?= escape($document->title); ?>" />
    </div>
</div>

<div class="k-form-group">
    <div class="k-input-group k-input-group--small">
        <label class="k-input-group__addon" for="docman_form_alias">
            <?= translate('Alias') ?>
        </label>
        <input id="docman_form_alias"
               type="text"
               class="k-form-control"
               name="slug"
               maxlength="255"
               value="<?= escape($document->slug) ?>"
               placeholder="<?= translate('Will be created automatically') ?>" />
    </div>
</div>

<?= import('com://admin/docman.document.default_field_file.html') ?>

<div class="k-form-group">
    <label><?= translate('Category'); ?></label>
    <?= helper('listbox.categories', array(
        'check_access' => true,
        'deselect' => false,
        'required' => true,
        'name' => 'docman_category_id',
        'filter' => array(
            'page' => isset($menu) ? $menu->id : null
        ),
        'disable_if_empty' => true,
        'attribs' => array(
            'required' => true,
            'id'    => 'docman_category_id'
        ),
        'selected' => $document->docman_category_id
  ))?>
</div>
