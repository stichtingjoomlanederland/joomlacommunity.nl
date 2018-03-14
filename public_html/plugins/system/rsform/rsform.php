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
        $pattern = '#\{rsform ([0-9]+)\}#i';
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

            $lang = JFactory::getLanguage();
            $lang->load('com_rsform', JPATH_SITE);

            foreach ($matches[0] as $j => $match)
            {
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

                $formId = $matches[1][$j];
                $content = str_replace($matches[0][$j], RSFormProHelper::displayForm($formId,true), $content);
            }

            $html = isset($head) ? ($head . '</head>' . $content) : $content;

            JFactory::getApplication()->setBody($html);

            RSFormProAssets::render();
        }
    }
}