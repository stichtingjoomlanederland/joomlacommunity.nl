<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class plgButtonDoclink extends JPlugin
{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onDisplay($name)
    {
        $button = new JObject();
        $button->class = 'btn';

        $is_joomlatools_extension = false;

        try
        {
            if (class_exists('Koowa') && class_exists('KObjectManager')) {
                $is_joomlatools_extension = (boolean) KObjectManager::getInstance()->isRegistered('dispatcher');
            }
        } catch (Exception $e) {}

        if (!$is_joomlatools_extension) { // Use Joomla modal
            $button->set('modal', true);
            $button->set('options', "{handler: 'iframe', size: {x: 1000, y: 600}}");
        }
        else $button->set('class', 'btn k-js-iframe-modal'); // Open using MagnificPopup

        $button->set('link', 'index.php?option=com_docman&amp;view=doclink&amp;e_name='.$name);
        $button->set('text', JText::_('PLG_DOCLINK_BUTTON_DOCUMENT'));
        $button->set('name', 'download');

        JHtml::_('stylesheet', 'media/koowa/com_koowa/css/modal-override.css');

        return $button;
    }
}
