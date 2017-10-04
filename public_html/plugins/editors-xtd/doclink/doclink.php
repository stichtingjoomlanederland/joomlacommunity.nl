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

        $button->set('modal', true);
        $button->set('link', 'index.php?option=com_docman&amp;view=doclink&amp;e_name='.$name);
        $button->set('text', JText::_('PLG_DOCLINK_BUTTON_DOCUMENT'));
        $button->set('name', 'download');
        $button->set('options', "{handler: 'iframe', size: {x: 800, y: 500}}");
        $button->set('onclick', 'joomlatoolsModalFixer(editor);');

        JFactory::getDocument()->addScriptDeclaration('
        function joomlatoolsModalFixer(editor) {
            if (typeof editor !== "undefined" && typeof editor.windowManager !== "undefined") {
                var i = 0;
                var interval = setInterval(function() {
                    i++;
                    var windows = editor.windowManager.getWindows();
                    if (windows.length) {
                        if (windows[0].$el) {
                            windows[0].$el.addClass("k-joomla-modal-override")
                        }
                        clearInterval(interval);
                    }
                    if (i == 40) {
                        clearInterval(interval);
                    }
                }, 50);
            }
        };
        ');

        JHtml::_('stylesheet', 'media/koowa/com_koowa/css/modal-override.css');

        return $button;
    }
}
