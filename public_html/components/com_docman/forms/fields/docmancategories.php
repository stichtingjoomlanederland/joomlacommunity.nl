<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmancategories extends JFormField
{
    protected $type = 'Docmancategories';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $key_field  = (string) $this->element['key_field'];
        $multiple   = (string) $this->element['multiple'] == 'true';
        $deselect   = (string) $this->element['deselect'] === 'true';
        $id         = isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_categories_select2';
        $pages      = isset($this->element['pages']) ? (string) $this->element['pages'] : null;

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/docman');

        $view = KObjectManager::getInstance()->getObject('com://admin/docman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        $attribs = array();
        if ($multiple) {
            $attribs['multiple'] = true;
            $attribs['size'] = $this->element['size'] ? $this->element['size'] : 5;
        }

        $attribs['id'] = $id;

        $value_field = $key_field ? $key_field : 'slug';
        $string = "
        <?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
        <?= helper('com://admin/docman.listbox.categories', array(
            'name' => \$el_name,
            'value' => \$value_field,
            'deselect' => \$deselect,
            'prompt'   => translate('All Categories'),
            'selected' => \$value,
            'filter'   => array(
                'page' => \$pages
            ),
            'attribs'  => \$attribs
        )); ?>

            <script>
                kQuery(function($){
                    $('#s2id_<?= \$id ?>').show();
                    $('#<?= \$id ?>_chzn').remove();
                });
            </script>
            ";

        return $template->loadString($string, 'php')
            ->render(array(
                'el_name'     => $el_name,
                'value'       => $value,
                'value_field' => $value_field,
                'deselect'    => $deselect,
                'attribs'     => $attribs,
                'id'          => $id,
                'pages'       => $pages
            ));
    }
}
