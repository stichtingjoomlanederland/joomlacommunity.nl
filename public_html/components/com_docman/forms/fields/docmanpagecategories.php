<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

JFormHelper::loadFieldClass('groupedlist');

class JFormFieldDocmanpagecategories extends JFormField
{
    protected $type = 'Docmanpagecategories';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $multiple = (string) $this->element['multiple'] == 'true';
        $deselect =  (string) $this->element['deselect'] === 'true';
        $id =  isset($this->element['id']) ? (string) $this->element['element_id'] : 'docman_page_categories_select2';
        $tree = isset($this->element['tree']) && $this->element['tree'] == 'true' ? true : false;

        KObjectManager::getInstance()->getObject('translator')->load('com://admin/docman');

        $view = KObjectManager::getInstance()->getObject('com://admin/docman.view.default.html');
        $template = $view->getTemplate()
            ->addFilter('style')
            ->addFilter('script');

        $attribs = array('class' => 'select2-listbox', 'id' => $id, 'multiple' => $multiple);

        // Grab selected page.
        $page = @$this->form->getValue("params")->page;

        if (empty($page)) $page = 'all';

        $string = "
        <?= helper('ui.load', array('styles' => array('file' => 'component'))); ?>
        <?= helper('listbox.pagecategories', array(
            'prompt' => translate('All Categories'),
            'page' => \$page,
			'select2' => true,
			'tree' => \$tree,
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

        $url = JRoute::_('index.php?option=com_docman&view=categories&format=json', false);

        $string .= "
            <script>
                kQuery(function($){
                    $('#docman_page_select2').change(function(e) {
                        var url = $(this).val() ? '$url' + '&page=' + $(this).val() : '$url';
                        $.ajax(url, {
                            success: function(data) {
                                var select = $('#$id');
                                select.empty();
                                $.each(data.entities, function(idx, entity) {
                                    select.append('<option value=\"'+entity.id+'\">'+entity.hierarchy_title+'</option>');
                                });
                                // Reset selection
                                select.val('');
                            }
                        });
                    });
                });
            </script>";

        return $template->loadString($string, 'php')
                 ->render(array(
                     'tree' => $tree,
                     'page'         => $page,
                     'el_name'      => $el_name,
                     'value'        => $value,
                     'deselect'     => $deselect,
                     'attribs'      => $attribs,
                     'id'           => $id
                 ));
    }
}
