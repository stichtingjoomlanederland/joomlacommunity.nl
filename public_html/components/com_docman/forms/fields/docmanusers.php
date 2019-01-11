<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmanusers extends JFormField
{
    protected $type = 'Docmanusers';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $multiple = (string) $this->element['multiple'] == 'true';
        $deselect =  (string) $this->element['deselect'] === 'true';
        $id =  isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_users_select2';

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/docman');

        $view = KObjectManager::getInstance()->getObject('com://admin/docman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        $attribs = array('class' => 'select2-listbox', 'id' => $id, 'multiple' => $multiple);

        $string = "
        <?= helper('behavior.jquery'); ?>
        <?= helper('listbox.users', array(
            'prompt' => translate('All Users'),
			'autocomplete' => true,
            'name' => \$el_name,
            'deselect' => \$deselect,
            'selected' => \$value,
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
                'el_name'  => $el_name,
                'value'    => $value,
                'deselect' => $deselect,
                'attribs'  => $attribs,
                'id'       => $id
            ));
    }
}
