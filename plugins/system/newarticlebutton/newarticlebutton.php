<?php
defined('_JEXEC') or die;

class PlgSystemNewArticleButton extends JPlugin
{
    protected $autoloadLanguage = true;

    public function onBeforeRender()
    {
        // get information
        $doc    = JFactory::getDocument();
        $user   = JFactory::getUser();
        $option = JRequest::getCmd( 'option' );
        $view   = JRequest::getCmd( 'view' );

        // check if user is guest
        if($user->guest) {
            return true;
        }

        // check if user can edit
        if(!$user->authorise('core.edit.own', 'com_content')) {
            return true;
        }

        // check if current page is a category blog
        if ($option == 'com_content' && $view == 'category')
        {
            $catid   = JRequest::getVar('id');

			// Get category name
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('title'));
			$query->from($db->quoteName('#__categories'));
			$query->where($db->quoteName('id') . ' = ' . $catid);

			// Send query
			$db->setQuery($query);
			$catname = $db->loadResult();

        	$editurl = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid=' . $catid);

            // create output
            $output = '<a href="' . $editurl . '" class="btn btn-success btn-new-article"><span class="fa fa-plus"></span>';

			// Button text
			if (!empty($this->params->get('buttontext'))) {
				$output .= $this->params->get('buttontext');
			}
			elseif ($this->params->get('catname'))
			{
				$output .= JText::sprintf('PLG_SYSTEM_NEWARTICLEBUTTON_CATEGORY', $catname);
			}
			else
			{
				$output .= JText::_('PLG_SYSTEM_NEWARTICLEBUTTON_DEFAULT_TEXT');
			}

			$output .= '</a>';
            $output .= $doc->getBuffer('component');

            // place button
            $doc->setBuffer($output, "component");
        }
    }
}
?>
