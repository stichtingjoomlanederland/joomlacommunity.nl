<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class JFormFieldDocmanlimit extends JFormField
{
    protected $type = 'Docmanlimit';

    public function getInput()
    {
        if (!class_exists('Koowa')) {
            return '';
        }

        $selected = $this->value;
        $name     = $this->name;
        $options  = array();

        $manager    = KObjectManager::getInstance();
        $translator = $manager->getObject('translator');
        $helper     = $manager->getObject('com://admin/docman.template.helper.select');

        $options[] = $helper->option(array(
            'label'  => $translator->translate('JGLOBAL_USE_GLOBAL'),
            'value' => 0));

        foreach (array(5, 10, 15, 20, 25, 30, 50, 100) as $value)
        {
            $options[] = $helper->option(array('label' => $translator->translate('J' . $value), 'value' => $value));
        }

        return $helper->optionlist(array(
            'options'  => $options,
            'name'     => $name,
            'selected' => $selected
        ));
    }
}
