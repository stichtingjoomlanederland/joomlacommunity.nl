/**
 * DOCman Sidebar
 *
 * Customized wrapper for advanced layout logic and behavior for the sidebar.
 * Uses Bootstrap Affix to make sure the sidebar never scrolls out of view (@TODO can't wait to use CSS4 position:sticky)
 * Makes sure that the sidebar height matches viewport height.
 * Allows advanced layout like locking page resize to only resize a mootree list, so that other parts of the sidebar is always visible.
 * Specializes Affix plugin to work on pages that are responsive, where static top and bottom values would break the layout.
 * Contains workarounds for Joomla! 3.0 responsive layout hacks that otherwise screws up the sidebar positioning is fixed.
 *
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @requires    Bootstrap Affix plugin
 */

var DOCman = DOCman || {};

(function($){

    /* Defining the constructor (same as initialize in MooTools Class) */
    /**
     * @param options
     * @constructor
     */
    DOCman.Sidebar = function(options){

        //@TODO 'limit' option is automatically passed to php behavior
        if(options.hasOwnProperty('offset') && !$.isPlainObject(options.offset)) delete options.offset;

        this.setOptions(options);

        this.sidebar  = $(this.options.sidebar);
        this.target   = this.sidebar.find(this.options.target);
        this.siblings = this.target.siblings();

        this.observe = this.options.observe ? $(this.options.observe) : this.sidebar.next();

        //Setup the inner container
        this.target.css('overflow', 'auto');

        //Check if the height is sufficient
        if(this.options.affix) this.options.affix = this.observe.outerHeight() > $(window).height();
        this.affix = this.options.affix;

        this.setHeight();

        if(this.options.affix && this.observe.outerHeight()) {
            this.sidebar.affix({
                offset: {
                    top: $.proxy(function(){
                        if(this.affix) {
                            return this.sidebar.parent().offset().top - this.options.offset.top;
                        } else {
                            return $(document).height();
                        }
                    }, this),
                    bottom: $.proxy(function(){
                        return $(document).height() - (this.observe.offset().top + this.observe.outerHeight()) + this.options.offset.bottom;
                    }, this)
                }
            });
        }

        $(window)
            .on('resize', $.proxy(this.setHeight, this))
            .on('sidebar:setHeight', $.proxy(this.setHeight, this))
            .on('sidebar:resizeHeight', $.proxy(function(){
                //console.log(arguments, this);
                this.resizeHeight();
            }, this));

        this.makeCollapsible();
    };

    DOCman.Sidebar.prototype = {

        /**
         * @constructs DOCman.Sidebar
         */
        constructor: DOCman.Sidebar,

        /**
         * @method getDefaults
         * @returns {{minHeight: number, sidebar: string, target: string, observe: boolean, affix: boolean, offset: {top: number, bottom: number, callback: Function}}}
         */
        getDefaults: function(){

            /** @lends DOCman.Sidebar.options */
            var defaults = {
                minHeight: 200,
                sidebar: '.sidebar',
                target: '.sidebar-inner',
                padding: {bottom: 50},
                observe: false, //Pass a css selector if the content area isn't the sidebars next sibling DOM element
                affix: false, //If Bootstrap's Affix plugin is loaded, this option will make the sidebar scroll with the view
                offset: {top: 0, bottom: 0, callback: function(){}}
            };

            if($('.navbar').length) {
                /**
                 * @lends DOCman.Sidebar.options.offset.callback
                 * @this DOCman.Sidebar
                 */
                defaults.offset.callback = function(){
                    this.options.offset.top = this.options.offset.bottom = 0;
                    /**
                     * Workarounds for possible fixed UI elements to calculate correct offsets
                     */
                    if($('.navbar-fixed-top').css('position') == 'fixed') {
                        /**
                         * Top navbar is fixed on desktop layout, static on mobile and tablet
                         * @type {number}
                         */
                        this.options.offset.top += $('.navbar.navbar-fixed-top').outerHeight();
                    }
                    var mobile_layout = this._isMobileLayout();
                    if(mobile_layout) {
                        if(mobile_layout === 1) {
                            //Mobile layout not enabled
                            this.options.offset.top += $('.subhead-collapse .subhead').outerHeight();
                            this.affix = true;
                        } else {
                            //Mobile layout is being enabled
                            this.affix = false;
                        }
                    }
                    //Navbar at the bottom may exist in both desktop and tablet layout
                    if($('.navbar-fixed-bottom').is(':visible')) {
                        /**
                         * Bottom navbar is fixed on desktop layout and sometimes on tablet laout, always static on mobile
                         * @type {number}
                         */
                        this.options.offset.bottom += $('.navbar-fixed-bottom').outerHeight();
                    }
                }
            }

            return defaults;
        },

        setOptions: function(options){

            this.options = $.extend(true, {}, this.getDefaults(), options);

            return this;
        },

        makeCollapsible: function() {
            var sidebar = $('#documents-sidebar'), storage = window.localStorage || {}, duration;
            sidebar.find('h3').each(function(i, h3){
                var $h3 = $(h3), key = 'DOCman#documents-sidebar['+i+']', state = storage[key] != 'closed' ? 'open' : 'closed', panel = $h3.next();

                panel.addClass('sidebar-panel').data('height', panel.height());

                if(!duration) {
                    panel.addClass('transition-enabled');
                    duration = parseFloat(panel.css('transition-duration'), 10)*1000;
                    panel.removeClass('transition-enabled');
                }

                $h3.addClass(state).on('click', function(){
                    $(this).toggleClass('open closed');

                    /** trying to animate the height change
                     if(!$(this).is('.k-js-category-tree') && $('.k-js-category-tree').is(':visible')) {
                if($(this).hasClass('open')) {
                    console.warn('ioenung!', '+='+$(this).outerHeight()+'px');
                    $('.k-js-category-tree').animate({height: '+='+$(this).outerHeight()+'px'}, duration);
                } else {
                    $('.k-js-category-tree').animate({height: '-='+$(this).outerHeight()+'px'}, duration);
                    console.log('-='+$(this).outerHeight()+'px');
                }
            }
                     //*/

                    if($(this).hasClass('closed')) {
                        panel.css('height', '');
                        panel.data('height', panel.height());
                        panel.css('height', panel.height()).addClass('transition-enabled');

                        setTimeout(function(){
                            panel.addClass('sidebar-panel-closed').css('height', '0px');
                        }, 0);

                        setTimeout(function() {
                            panel.removeClass('transition-enabled');
                        }, duration+100);
                    } else {

                        panel.addClass('transition-enabled');
                        setTimeout(function(){
                            panel.removeClass('sidebar-panel-closed').css('height', panel.data('height'));
                        }, 0);

                        setTimeout(function() {
                            panel.removeClass('transition-enabled').css('height', '');
                            panel.data('height', panel.height());
                        }, duration+100);
                    }

                    setTimeout(function() {
                        //Resets the height of the categories tree
                        //$(window).trigger('sidebar:resizeHeight', [to_height]); //@TODO animate height change to categories list
                        $(window).trigger('sidebar:setHeight');
                    }, duration+100);

                    storage[key] = $h3.hasClass('open') ? 'open' : 'closed';
                });
                if(state == 'closed') {
                    panel.data('height', panel.height());
                    panel.addClass('sidebar-panel-closed').css('height', '0px');
                }
            });
        },

        /**
         * @method setHeight
         *
         */
        setHeight: function(){

            if(this.options.offset.callback && this.options.offset.callback.call) this.options.offset.callback.call(this);

            if(this.options.affix) {
                //Making sure it's positioned correctly
                $(window).trigger('scroll.affix.data-api');

                //Set the right horizontal offset, this changes as the sidebar collapses in responsive layouts
                this.sidebar.css({left: this.sidebar.parent().offset().left+1, top: this.options.offset.top});
            }

            // if mobile layout, remove extra height or reveal overflow
            var mobile_layout = this._isMobileLayout();
            if(this.options.affix && mobile_layout > 1) {

                this.target.css('max-height', '');

            } else {

                this.affix = true;

                var offset = 0;
                if(this.siblings.length) {
                    this.siblings.each(function(){
                        if($(this).is(':visible')) offset += $(this).outerHeight();
                    });
                }

                //Set the right vertical offset, this changes as the sidebar collapses in responsive layouts
                offset += this.options.offset.top + this.options.offset.bottom + this.options.padding.bottom;

                var height = $(window).height() - offset;
                this.target.css('max-height', Math.max(height, this.options.minHeight));
            }

            //To allow listening to height changes
            this.target.triggerHandler('resize');
        },

        /**
         * Used to update the height, allowing it to be animated
         * @param height
         */
        resizeHeight: function(height){

        },

        /**
         * Checks if there's a responsive mobile layout available, and whether it's active
         * @returns {number} 0 = unavailable, 1 = available but disabled, 2 = enabled
         * @private
         */
        _isMobileLayout: function(){

            var result = 0;
            if($('.subhead-collapse .subhead').length) {
                result = 1;
                /**
                 * Top navbar is fixed on desktop layout, static on mobile and tablet
                 * NOTE: Hacky but thanks to how the subhead js logic in j!3.0 works we need to do it this way
                 */
                var test = $('<div/>', {'class': 'subhead subhead-fixed'}).appendTo(document.body);
                if(test.css('position') != 'fixed') {
                    result = 2;
                }
                test.remove();
            }

            return result;
        }
    };
}(window.kQuery));