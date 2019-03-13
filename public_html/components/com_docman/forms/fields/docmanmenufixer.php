<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * This makes sure the form validates for existing menu items. Otherwise Joomla leaves the type field empty.
 *
 * Also adds some basic styling to parameters
 */
class JFormFieldDocmanmenufixer extends JFormField
{
    protected $type = 'Docmanmenufixer';

    protected function getInput()
    {
        $name = (string) $this->element['view'];

        $html = '
        <style type="text/css">#attrib-basic .control-group .control-label { width: 250px !important; }</style>
        <span class="js-docman-menu-fixer-anchor" style="display: none"></span>
        <script type="text/javascript">
            jQuery(function($) {' .
                (!empty($name) ? 'jSelectPosition_jform_type('.json_encode(JText::_($name)).');' : '')
            . '
            
                var group = $(".js-docman-menu-fixer-anchor").parents("div.control-group");

                if (group.length === 1) {
                    group.hide();
                }
            });
        </script>
        
        <script type="text/javascript">
                
        jQuery(function($) {
        
            var last_key, key, show_list, hide_list, onChange, title, layout, hide;
        
            title  = $("#jform_params_document_title_link");
            layout = $("#jform_request_layout");
            
            if (title.length && layout.length) {
                hide = {
                    "table:download": [
                        "#jform_params_show_document_title",
                        "#jform_params_show_document_image",
                        "#jform_params_show_document_tags",
                        "#jform_params_show_document_created_by",
                        "#jform_params_show_document_description",
                        "#jform_params_show_document_modified",
                        "#jform_params_show_player"
                    ],
                    "gallery:download": [
                        "#jform_params_show_document_tags",
                        "#jform_params_show_document_created",
                        "#jform_params_show_document_created_by",
                        "#jform_params_show_document_description",
                        "#jform_params_show_document_modified",
                        "#jform_params_show_document_filename",
                        "#jform_params_show_document_size",
                        "#jform_params_show_document_hits",
                        "#jform_params_show_document_extension",
                        "#jform_params_show_player"
                    ],
                    "default:download": [
                        "#jform_params_show_player"
                    ]
                };
            
                onChange = function() {
                    key = layout.val()+\':\'+title.val();
                    show_list = (typeof hide[last_key] !== "undefined" && last_key !== key) ? hide[last_key] : [];
                    hide_list = typeof hide[key] !== "undefined" ? hide[key] : [];
            
                    $.each(show_list, function(i, selector) {
                        if ($.inArray(selector, hide_list)) {
                            $(selector).parents(".control-group").show();
                        }
                    });
            
                    $.each(hide_list, function(i, selector) {
                        $(selector).parents(".control-group").hide();
                    });
            
                    last_key = key;
                };
            
                title.change(onChange);
                layout.change(onChange);
            
                onChange();
            }
        }); 
        </script>
        ';

        return $html;
    }
}
