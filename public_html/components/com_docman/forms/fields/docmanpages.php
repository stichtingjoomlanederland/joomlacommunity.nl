<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldDocmanpages extends JFormField
{
    protected $type = 'Docmanpages';

    /**
     * Wraps the output in com_docman class for Bootstrap and removes Chosen
     */
    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $options = array();

        if ($this->element->option)
        {
            foreach ($this->element->option as $option)
            {
                $options[] = array(
                    'value' => (string)$option['value'],
                    'label' => JText::_((string)$option)
                );
            }
        }

        $value    = $this->value;
        $el_name  = $this->name;

        $types    = ((string) $this->element['types']) ? explode(',', $this->element['types']) : array();
        $multiple = (string) $this->element['multiple'] == 'true';
        $deselect = (string) $this->element['deselect'] === 'true';
        $id       = isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_page_select2';

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/docman');

        $view = KObjectManager::getInstance()->getObject('com://admin/docman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        $attribs = array('class' => 'select2-listbox', 'id' => $id, 'multiple' => $multiple);

        return $template
            ->loadString($this->getTemplateContent(), 'php')
            ->render(array(
                'el_name'  => $el_name,
                'value'    => $value,
                'deselect' => $deselect,
                'attribs'  => $attribs,
                'id'       => $id,
                'types'    => $types,
                'options'  => $options
            ));
    }

    protected function getTemplateContent()
    {
        $pages = KObjectManager::getInstance()->getObject('com://admin/docman.model.pages')
            ->language('all')->access(-1)->fetch();

        if (count($pages) === 0)
        {
            return "
            <?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
            <div class=\"alert alert-error no_menu_items_layout\">
                <h4><?= translate('No menu items found') ?></h4>
                <p><?= translate('Docman menu warning'); ?></p>
                <p><?= translate('Docman menu warning instruction'); ?></p>
                <p><a href=\"<?= JRoute::_('index.php?option=com_menus&view=items'); ?>\" class=\"btn btn-primary\"><?= translate('Go to menu manager') ?></a></p>
            </div>
            ";
        }

        $string = "
        <?= helper('behavior.jquery'); ?>
        <?= helper('listbox.pages', array(
            'prompt' => translate('All pages'),
            'name' => \$el_name,
            'deselect' => \$deselect,
            'selected' => \$value,
            'attribs'  => \$attribs,
            'types'    => \$types,
            'options'  => \$options
        )); ?>

            <script>
                kQuery(function($){
                    $('#s2id_<?= \$id ?>').show();
                    $('#<?= \$id ?>_chzn').remove();
                });
            </script>
            ";

        return $string;
    }
}
