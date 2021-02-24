<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA and Contributors. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.eu
 */

class ComDocmanTemplateHelperModal extends ComFilesTemplateHelperModal
{
    public function icon($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'name' => '',
            'attribs' => array(),
            'visible' => true,
            'link' => '',
            'callback' => 'Docman.selectIcon'
        ))->append(array(
            'id' => $config->name,
            'value' => $config->name
        ));

        if ($config->callback) {
            $config->link .= '&callback='.urlencode($config->callback);
        }

        $attribs = $this->buildAttributes($config->attribs);

        $link = '<a class="mfp-iframe" data-k-modal="%s" href="%s">%s</a>';
        $html = sprintf($link, htmlentities(json_encode(array('mainClass' => 'koowa_dialog_modal'))), $config->link, $config->link_text);
        $input = '<input name="%1$s" id="%2$s" value="%3$s" %4$s size="40" %5$s style="display:none" />';
        $html .= sprintf($input, $config->name, $config->id, $config->value, $config->visible ? 'type="text" readonly' : 'type="hidden"', $attribs);

        $html .= $this->getTemplate()->createHelper('behavior')->modal();

        return $html;
    }
}
