<?php
/**
 * @package RSForm!Pro
 * @copyright (C) 2007-2017 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSForm! Pro system plugin
 */
class plgSystemRSForm extends JPlugin
{
    public function onAfterRender()
    {
        $mainframe = JFactory::getApplication();

        // No HTML content, no need for forms
        if (JFactory::getDocument()->getType() != 'html')
        {
            return false;
        }

        // Backend doesn't need forms being loaded
        if ($mainframe->isAdmin())
        {
            return false;
        }

        // Are we editing an article?
        $option = $mainframe->input->get('option');
        $task   = $mainframe->input->get('task');
        if ($option == 'com_content' && $task == 'edit')
        {
            return false;
        }

        $html = JFactory::getApplication()->getBody();

        if (strpos($html, '</head>') !== false)
        {
            list($head, $content) = explode('</head>', $html, 2);
        }
        else
        {
            $content = $html;
        }

        // Something is wrong here
        if (empty($content))
        {
            return false;
        }

        // No placeholder, don't run
        if (strpos($content, '{rsform ') === false)
        {
            return false;
        }

        // expression to search for
        $pattern = '#\{rsform ([0-9]+)(.*?)?\}#i';
        if (preg_match_all($pattern, $content, $matches))
        {
            if (file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php'))
            {
                require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/rsform.php';
            }

            if (!class_exists('RSFormProAssets') || !class_exists('RSFormProHelper'))
            {
                return true;
            }

            RSFormProAssets::$replace = true;

	        JFactory::getLanguage()->load('com_rsform', JPATH_SITE);

            foreach ($matches[0] as $i => $match)
            {
	            $attributes = trim($matches[2][$i]);
	            if (strlen($attributes) && preg_match_all('#[a-z0-9_\-]+=".*?"#i', $attributes, $attributesMatches))
	            {
		            $data = array();

		            foreach ($attributesMatches[0] as $pair)
		            {
			            list($attribute, $value) = explode('=', $pair, 2);

			            $attribute  = trim(html_entity_decode($attribute));
			            $value 		= html_entity_decode(trim($value, '"'));

			            if (isset($data[$attribute]))
			            {
				            if (!is_array($data[$attribute]))
				            {
					            $data[$attribute] = (array) $data[$attribute];
				            }

				            $data[$attribute][] = $value;
			            }
			            else
			            {
				            $data[$attribute] = $value;
			            }
		            }

		            if ($data)
		            {
			            JFactory::getApplication()->input->get->set('form', $data);
		            }
	            }

                // within <textarea>
                $tmp = explode($match, $content, 2);
                $before = strtolower(reset($tmp));
                $before = preg_replace('#\s+#', ' ', $before);

                // we have a textarea
                if (strpos($before, '<textarea') !== false)
                {
                    // find last occurrence
                    $tmp = explode('<textarea', $before);
                    $textarea = end($tmp);
                    // found & no closing tag
                    if (!empty($textarea) && strpos($textarea, '</textarea>') === false)
                        continue;
                }

                $formId = $matches[1][$i];
                $content = str_replace($matches[0][$i], RSFormProHelper::displayForm($formId,true), $content);
            }

            $html = isset($head) ? ($head . '</head>' . $content) : $content;

            JFactory::getApplication()->setBody($html);

            RSFormProAssets::render();
        }
    }
}