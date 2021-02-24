<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmanfolders extends JFormField
{
    protected $type = 'Docmanfolders';

    protected function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $value = $this->value;
        $el_name = $this->name;

        $multiple = (string) $this->element['multiple'] == 'true';
        $deselect =  $this->element['deselect'] === 'true';

        $attribs = array();
        if ($multiple) {
            $attribs['multiple'] = true;
            $attribs['size'] = $this->element['size'] ? $this->element['size'] : 5;
        }

        return KObjectManager::getInstance()->getObject('com://admin/docman.template.helper.listbox')->folders(array(
            'name' => $el_name,
            'selected' => $value
        ));
    }
}
