<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>


<script>
(function($) {

    /** @namespace Docman */
    if (typeof Docman === 'undefined') { //noinspection JSUndeclaredVariable
        Docman = {};
    }

    Docman.Folder = Koowa.Class.extend({
        active: null,
        getOptions: function() {
            return {
                category: '#category',
                form:     '.k-js-form-controller'
            };
        },
        initialize: function(element, options) {
            var self = this;

            this.supr();
            this.setOptions(options);

            this.element = element = $(element);

            this.elements = {
                current_folder: element.find('.current-folder'),
                custom_container: element.find('.folder-custom-container'),
                custom:  element.find('#folder-custom-input'),
                automatic: element.find('input#automatic_folder1'),
                switch_automatic: element.find('input#automatic_folder1'),
                switch_custom: element.find('input#automatic_folder0'),
                category: $(self.options.category),
                form: $(self.options.form)
            };

            this.category_id = this.elements.category.val();
            this.folder      = this.elements.current_folder.text();

            if (this.elements.switch_automatic.prop('checked')) {
                this.switchTo('automatic');
            }
            else {
                this.switchTo('custom');
            }

            this.elements.category.on('change', $.proxy(this.updateCurrent, this));

            this.elements.switch_automatic.click(function() {
                self.switchTo('automatic');
            });
            this.elements.switch_custom.click(function() {
                self.switchTo('custom');
            });

            if (this.elements.form) {
                var beforeSend = function() {
                    var automatic = self.elements.switch_automatic,
                        form      = $(this);

                    if (!automatic.prop('checked')) {
                        automatic.attr('name', '');
                        $('<input type="hidden" name="automatic_folder" />').val(0).appendTo(form);
                    }
                };

                this.elements.form.on('k:beforeApply', beforeSend)
                    .on('k:beforeSave', beforeSend)
                    .on('k:beforeSave2new', beforeSend);
            }
        },
        updateCurrent: function() {
            var folder = this.folder;

            if (this.active === 'automatic') {
                if (this.elements.category.val() != this.category_id || !this.folder) {
                    folder = '<em>'+Koowa.translate('Please save to calculate')+'</em>';
                }
            }

            this.elements.current_folder.html(folder);
        },
        switchTo: function(active) {
            this.active = active;

            if (active === 'automatic') {
                this.switchToAutomatic();
            }
            else if (active === 'custom') {
                this.switchToCustom();
            }
        },
        switchToAutomatic: function() {
            this.elements.switch_custom.prop('checked', false);
            this.elements.switch_automatic.prop('checked', true);

            this.updateCurrent();

            this.elements.current_folder.show();

            this.elements.custom_container.hide();
        },
        switchToCustom: function() {
            this.elements.switch_automatic.prop('checked', false);
            this.elements.switch_custom.prop('checked', true);

            this.elements.current_folder.hide();

            this.elements.custom_container.show();
        }
    });

    $(function() {
        new Docman.Folder('.js-folder-container');
    });

})(window.kQuery);

</script>


<div class="js-folder-container">

    <div class="k-form-group">
        <div class="k-optionlist k-optionlist--neutral">
            <div class="k-optionlist__content">
                <input type="radio" name="automatic_folder" id="automatic_folder1" value="1"
                    <?= $category->isNew() || $category->automatic_folder ? 'checked' : '' ?>
                />
                <label for="automatic_folder1"><?= translate('Automatic') ?></label>
                <input type="radio" id="automatic_folder0" value="0" />
                <label for="automatic_folder0"><?= translate('Custom') ?></label>
            </div>
        </div>
    </div>

    <div class="k-form-group">
        <label>
            <?= translate('Current'); ?>
        </label>
        <div class="folder-custom-container" style="display: none">
            <?= helper('listbox.folders', array(
                'name' => 'folder',
                'attribs' => array('id' => 'folder-custom-input'),
                'selected' => $category->folder,
                'select2'  => true
            )) ?>
        </div>
        <div class="current-folder" style="display: none"><?= $category->folder ?></div>
    </div>

</div>
