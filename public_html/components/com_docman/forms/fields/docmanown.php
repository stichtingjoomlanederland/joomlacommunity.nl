<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('radio');

class JFormFieldDocmanown extends JFormFieldRadio
{
    protected $type = 'Docmanown';

    protected function getInput()
    {
        $html = parent::getInput();

        // Automatically hide/show the owners list filter depending on chosen value.
        $html .= '<script>
            kQuery(function($) {
                var users_listbox = $("#docman_users_select2").parents("div.control-group"),
                    hide = function() {
                        users_listbox.hide();
                    },
                    show = function() { users_listbox.show(); },
                    own  = $(\'input[name="' . $this->name . '"]:checked\').val();

                if (!users_listbox.length) {
                    users_listbox = $("#docman_users_select2").parents("li")
                }

                if (own == 1) {
                    hide();
                }

                $("#' . $this->id . '0").click(function() {
                    hide();
                });

                $("#' . $this->id . '1").click(function() {
                    show();
                });
            });
        </script>';

        return $html;
    }
}