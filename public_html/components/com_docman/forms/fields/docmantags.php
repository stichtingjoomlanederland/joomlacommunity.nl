<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmantags extends JFormField
{
    protected $type = 'Docmantags';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $id    = isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_tags_select2';
        $pages = isset($this->element['pages']) ? (string) $this->element['pages'] : null;

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/docman');

        $view = KObjectManager::getInstance()->getObject('com://admin/docman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        $string = "
        <?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
        <?= helper('com://admin/docman.listbox.tags', array(
            'autocreate' => false,
            'name' => \$el_name,
            'value' => 'slug',
            'selected' => \$value,
            'filter'   => array(
                'page' => \$pages
            ),
            'attribs'  => array(
                'id' => \$id,
                'data-placeholder' => translate('All Tags'))
        )); ?>

            <script>
                kQuery(function($){
                    $('#s2id_<?= \$id ?>').show();
                    $('#<?= \$id ?>_chzn').remove();
                });
            </script>";

        return $template->loadString($string, 'php')
            ->render(array(
                'el_name'     => $el_name,
                'value'       => $value,
                'id'          => $id,
                'pages'       => $pages
            ));
    }
}
