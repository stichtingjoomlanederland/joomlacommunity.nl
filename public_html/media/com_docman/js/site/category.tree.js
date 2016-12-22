/**
 * DOCman Categories Tree - Site
 *
 * Customized instance of jqTree to render a list of categories in a tree structure.
 * It deals with turning a flat list into a hierarchy structure that jqTree understands.
 * And it changes the default styling and behavior to match the general DOCman GUI.
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    Koowa.Class, jqTree plugin
 */

var DOCman = DOCman || {};
if(!DOCman.hasOwnProperty('Tree')) DOCman.Tree = {};

(function($){

    DOCman.Tree.CategoriesSite = Koowa.Tree.extend({
        getDefaults: function() {
            var defaults = this.supr();

            defaults.autoOpen = false;

            return defaults;
        },
        attachHandlers: function(){

            this._attachHandlers(); // Attach needed events from DOCman.Tree.Categories._attachHandlers

            var self = this;

            this.element.bind({
                'tree.init':
                    function() {
                        /**
                         * Select the root node, if no other node is selected
                         */
                        if(!$(this).tree('getSelectedNode')) $(this).tree('selectNode', $(this).tree('getNodeById', -1));

                        /**
                         * Attach select event, after a potential select event fired to hilite the root node
                         */
                        self.element.bind('tree.select', function(event){
                            if(event.node) { // When event.node is null, it's actually a deselect event
                                var node = event.node;

                                if (node.id != -1 && node.route) {
                                    window.location = node.route;
                                }
                            }
                        });

                        /**
                         * Sidebar.js will fire a resize event when it sets the height on load, we want our animated scroll
                         * to happen after that, but not on future resize events as it would confuse the user experience
                         */
                        self.element.one('resize', function(){
                            if(self.tree('getSelectedNode')) {
                                self.scrollIntoView(self.tree('getSelectedNode'), self.element, 900);
                            }
                        });
                    }
            });

        }
    });
}(window.kQuery));