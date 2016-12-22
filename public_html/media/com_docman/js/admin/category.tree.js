/**
 * DOCman Categories Tree
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

    DOCman.Tree.Categories = Koowa.Tree.extend({

        getDefaults: function(){

            var self = this,
                defaults = {
                    autoOpen: 0, //Auto open just "All Categories" by default, this value is the nesting level not the node id
                    lang: { //l18n strings
                        root: 'All Categories'
                    },
                    state: 'category'
                };

            return $.extend(true, {}, this.supr(), defaults); // get the defaults from the parent and merge them
        },

        /* Wraps the parsed data into a root node with the label 'All Categories' */
        parseData: function(list){
            return [{
                label: this.options.lang.root,
                id: -1, //negative 1 used as 0 doesn't work with this.selectNode
                children: this._parseData(list)
            }];
        },

        attachHandlers: function(){

            this._attachHandlers(); // Attach needed events from Koowa.Tree._attachHandlers

            var self = this, query_data = window.location.search ? self.unserialize(window.location.search) : {};

            this.element.bind({
                'tree.open': // Animate a scroll to the node being opened so child elements scroll into view
                function(event) {
                    self.scrollIntoView(event.node, self.element, 300);
                },
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
                            var node = event.node,
                                id = node.id > 0 ? node.id : ''; //The root node id is -1, but should be '' in the url

                            query_data[self.options.state] = id;

                            window.location.search = $.param(query_data); //We're only changing the search query part of the url
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
