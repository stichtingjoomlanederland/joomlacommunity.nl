<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFormLayouts extends JFormFieldList
{
	protected $type = 'FormLayouts';
	
	protected function getOptions()
    {
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

		// Initialize variables.
		$options = array();

		if ($allLayouts = RSFormProHelper::getFormLayouts())
        {
            if ($allLayouts['html5Layouts'])
            {
                foreach ($allLayouts['html5Layouts'] as $layout)
                {
                    $options[] = JHtml::_('select.option', $layout, JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)));
                }
            }
        }

        reset($options);

        return $options;
	}
}
