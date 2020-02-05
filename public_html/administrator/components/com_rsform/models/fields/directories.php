<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('JPATH_PLATFORM') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

JFormHelper::loadFieldClass('list');

class JFormFieldDirectories extends JFormFieldList
{
	protected $type = 'Directories';

	protected $firstValue;
	
	protected function getOptions()
    {
        $app        = JFactory::getApplication();
        $db         = JFactory::getDbo();
        $sortColumn = $app->getUserState('com_rsform.forms.filter_order', 'FormId');
        $sortOrder  = $app->getUserState('com_rsform.forms.filter_order_Dir', 'ASC');
        $options    = array();

        $query = $db->getQuery(true)
            ->select($db->qn('FormId'))
            ->select($db->qn('FormTitle'))
            ->select($db->qn('Lang'))
            ->from($db->qn('#__rsform_forms'))
            ->order($db->qn($sortColumn) . ' ' . $db->escape($sortOrder));
        if ($results = $db->setQuery($query)->loadObjectList())
        {
            $query->clear()
                ->select($db->qn('formId'))
                ->from($db->qn('#__rsform_directory'));

            $directories = $db->setQuery($query)->loadColumn();

            foreach ($results as $result)
            {
                $lang = RSFormProHelper::getCurrentLanguage($result->FormId);

                if ($lang != $result->Lang)
                {
                    if ($translations = RSFormProHelper::getTranslations('forms', $result->FormId, $lang))
                    {
                        foreach ($translations as $field => $value)
                        {
                            if (isset($result->$field))
                            {
                                $result->$field = $value;
                            }
                        }
                    }
                }

                $options[] = JHtml::_('select.option', $result->FormId, sprintf('(%d) %s', $result->FormId, $result->FormTitle), 'value', 'text', !in_array($result->FormId, $directories));
            }

            $first = reset($results);

            $this->firstValue = $first->FormId;
        }

		reset($options);
		
		return $options;
	}

	public function getInput()
	{
		$html = parent::getInput();

		if ($this->value)
		{
			$url = JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&formId=' . $this->value);
		}
		elseif ($this->firstValue)
		{
			$url = JRoute::_('index.php?option=com_rsform&view=directory&layout=edit&formId=' . $this->firstValue);
		}
		else
		{
			$url = '#';
		}

		$html .= ' <a id="directoryLink" target="_blank" href="' . $url . '" class="btn btn-primary">' . JText::_('COM_RSFORM_EDIT_DIRECTORY') . '</a>';

		JFactory::getDocument()->addScriptDeclaration("function generateDirectoryLink() { document.getElementById('directoryLink').setAttribute('href', 'index.php?option=com_rsform&view=directory&layout=edit&formId=' + document.getElementById('{$this->id}').value); }");

		return $html;
	}
}
