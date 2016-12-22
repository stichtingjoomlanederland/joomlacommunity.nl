<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
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

        $button->set('modal', true);
        $button->set('link', 'index.php?option=com_docman&amp;view=doclink&amp;e_name='.$name.'&amp;tmpl=koowa');
        $button->set('text', JText::_('PLG_DOCLINK_BUTTON_DOCUMENT'));
        $button->set('name', 'download');
        $button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");

        return $button;
    }
}
