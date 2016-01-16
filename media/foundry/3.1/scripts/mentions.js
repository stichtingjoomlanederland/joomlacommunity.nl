(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

/**
 * jquery.mentions.
 * Textarea with ability to highlight text blocks
 * Includes built-in autogrow and autocomplete
 * and a inspector utility for debugging purposes.
 *
 * Customizable trigger keys allows you to create
 * mentions, hashtags and nything else that fit your needs.
 *
 * Copyright (c) 2013 Jensen Tonne
 * http://www.jstonne.com
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * 
 * Trigger configuration:
 * 
 * type
 *   Name for the trigger type.
 * 
 * wrap
 *   Whether or not hitting a trigger key before existing 
 *   characters will wrap these characters into the block
 *   marker until a stop character is found. Space will
 *   always be a stop character whether or not it is
 *   specified in allowSpace or stop option. (default: false)
 *
 * stop
 *   A string of characters that will end the block. (default: "")
 *
 * allowSpace
 *   If true, hitting on a space in a block marker
 *   will not end the block marker until a consecutive
 *   space is pressed. (default: false)
 * 
 * query
 *   Accepts a url string, an array of objects or
 *   a function that returns a deferred object that
 *   resolves with an array of objects. Also accept a
 *   query configuration object (advanced users only!).
 *
 * finalize
 *    If true, after selecting an item from the
 *    autocomplete menu, the block marker is finalized
 *    and any further changes to within the block marker
 *    will simply destroy the entire marker. (default: false)
 *
 * triggers: {
 *     "@": {
 *         type: "entity",
 *         wrap: false,
 *         stop: "",
 *         allowSpace: true,
 *         query: http://json/rest/api?q=
 *     },
 *     "#": {
 *         type: "hashtag",
 *         wrap: true,
 *         stop: " #",
 *         allowSpace: false
 *     }
 * }
 *
 */var _backspace = "",
    _space     = " ",
    _nbsp      = "\u00a0",
    _newline   = "\n",
    KEYCODE = {
        BACKSPACE: 8,
        COMMA: 188,
        DELETE: 46,
        DOWN: 40,
        ENTER: 13,
        ESCAPE: 27,
        LEFT: 37,
        RIGHT: 39,
        SPACE: 32,
        TAB: 9,
        UP: 38
    };

// TODO: Put this elsewhere
$.fn.caret = function(start, end) {

    if (this.length == 0) return;
    if (typeof start == 'number')
    {
        end = (typeof end == 'number') ? end : start;
        return this.each(function ()
        {
            if (this.setSelectionRange)
            {
                this.setSelectionRange(start, end);
            } else if (this.createTextRange)
            {
                var range = this.createTextRange();
                range.collapse(true);
                range.moveEnd('character', end);
                range.moveStart('character', start);
                try { range.select(); } catch (ex) { }
            }
        });
    } else
    {
        if (this[0].setSelectionRange)
        {
            start = this[0].selectionStart;
            end = this[0].selectionEnd;
        } else if (document.selection && document.selection.createRange)
        {
            var range = document.selection.createRange();
            start = 0 - range.duplicate().moveStart('character', -100000);
            end = start + range.text.length;
        }
        return { start: start, end: end };
    }
};
var Marker = function(options) {
    $.extend(this, options);
}

$.extend(Marker.prototype, {

    val: function(str) {

        var marker = this;

        if (str===undefined) {
            return marker.text.nodeValue;
        }

        // Update text value
        marker.text.nodeValue = str;

        // Update end & length
        marker.end = marker.start + (marker.length = str.length);

        return marker;
    },

    insert: function(str, start, end) {

        // Marker
        var marker     = this,
            block      = marker.block,
            text       = marker.text,
            parent     = marker.parent,
            br         = marker.br,
            val        = marker.val(),
            length     = marker.length,

            // Character flags
            newline    = str==_newline,
            space      = str==_space,
            backspace  = str==_backspace,

            // Trigger
            trigger = marker.trigger || {},
            finalize = trigger.finalize,
            finalized = marker.finalized,

            // Spaces
            // We need to insert space in a spawned marker when:
            //  - space is not allowed in block marker
            //  - space is allowed in block marker
            //    but there's already a trailing space.
            trailingSpace = val.charCodeAt(start - 1)==32,
            allowSpace    = trigger.allowSpace || marker.allowSpace,
            spawnSpace    = space && (!allowSpace || (allowSpace && trailingSpace));

        // If no start position was given,
        // assume want to insert at the end of the text.
        if (start===undefined) start = length;

        // If no end position was given,
        // assume we want to insert in a single position.
        if (end===undefined) end = start;

        // If this block marker already has a trailing space
        // but the block marker hasn't been finalized yet.
        if (block && allowSpace && trailingSpace && !finalized) {

            // Reverse the insertion on textarea
            if (space) {

                var $textarea = $(marker.textarea),
                    wholeText = $textarea.val(),
                    pos       = $textarea.caret().end - 1,
                    offset    = marker.start + start;

                $textarea
                    .val(wholeText.substring(0, offset) + wholeText.slice(offset + 1))
                    .caret(pos);
            }

            // Convert to text marker
            marker.toTextMarker();

            // TODO: Refactor this
            if (space) {
                // Trigger marker for post processing
                $(parent).trigger("markerInsert", [marker, nodes, str, start, end]);
                return marker;
            // For other characters, restart text insertion process.
            } else {
                return marker.insert(str, start, end);
            }
        }

        // If we are at the end of a block marker OR this is a newline block marker,
        // space & newline should be added to beginning of the next marker.
        if (block && end==length && !backspace && (spawnSpace || newline || br || finalized)) {
            return marker.spawn().insert(str, 0);
        }

        // Quick monkey patch for typing before a block marker
        // in the beginning of the textarea.
        if (block && marker.index===0 && end===0 && str.length===1) {

            var textnode = document.createTextNode(str);
            parent.insertBefore(textnode, block);

            var newMarker = new Marker({
                index: 0,
                start: 0,
                end: str.length,
                text: textnode,
                parent: parent,
                textarea: marker.textarea,
                allowSpace: true,
            });

            // Trigger marker for post processing
            $(parent).trigger("markerInsert", [newMarker, nodes, str, start, end]);

            return newMarker;
        }

        // Nodes
        var next   = block ? block.nextSibling : text.nextSibling,

            // Text
            prefix = val.substring(0, start),
            suffix = val.slice(end),

            // Chunks
            // Replace double space with one space and one nbsp to ensure
            // overlay is rendered proper spacing + identical word-wrap.
            chunks = str.replace(/  /g, " " + _nbsp).split(_newline),
            nodes  = [],
            node   = block || text,
            i      = chunks.length;

        // Add the prefix/suffix to the first/last chunk.
        // If this is a single chunk, the suffix is
        // actually added to the same chunk. :)
        chunks[0] = prefix + chunks[0];
        chunks[i-1] += suffix;

        // If this is a single chunk, this loop won't execute
        // but we still benefit from having the correct index. :)
        while (--i) {

            var node = document.createTextNode(chunks[i]),
                br = document.createElement("BR");

            nodes.push(parent.insertBefore(node, next));
            nodes.push(parent.insertBefore(br, node));

            next = br;
        }

        // Update the text value in the current marker
        marker.val(chunks[i]);

        // Trigger marker for post processing
        $(parent).trigger("markerInsert", [marker, nodes, str, start, end]);

        return marker;
    },

    remove: function() {

        var marker = this,
            parent = marker.parent,
            block = marker.block,
            text = marker.text;

        if (block) $(block).trigger("triggerDestroy", [marker]);

        parent.removeChild(block || text);

        marker.removed = true;

        $(parent).trigger("markerRemove", [marker]);

        return marker;
    },

    toTextMarker: function() {

        var marker = this,
            block  = marker.block,
            parent = marker.parent;

        if (!block) return marker;

        // Create a copy of the old marker
        var old = marker.clone();

        if (block) $(block).trigger("triggerDestroy", [marker]);

        // Move the text node out and
        // place it before the next marker.
        parent.insertBefore(marker.text, block.nextSibling);

        // Remove the block node
        parent.removeChild(block);
        delete marker.block;
        delete marker.trigger;

        $(marker.parent).trigger("markerConvert", [marker, old, "text"]);

        return marker;
    },

    toBlockMarker: function(normalize) {

        var marker = this;

        // If this is a block marker, skip.
        if (marker.block) return;

        var old = marker.clone(),
            parent = marker.parent,
            block = marker.block = document.createElement("SPAN"),
            text  = marker.text;

        // Insert block before the next marker
        parent.insertBefore(block, text.nextSibling);

        // Move text inside block marker
        block.appendChild(text);

        // Create empty marker data
        $(block).data("marker", {});

        $(marker.parent).trigger("markerConvert", [marker, old, "block"]);

        return marker;
    },

    spawn: function(start, end) {

        var marker = this,
            text   = marker.text,
            parent = marker.parent,
            block  = marker.block,
            next   = block ? block.nextSibling : text.nextSibling;

        // If not start and end position was given, assume that
        // we're spawning an empty marker next to the current marker.
        // [hello] --> [hello[]
        if (start===undefined) {
            start = end = marker.length;
        }

        // If we're spawning in text in the middle,
        // split out the end marker and insert it before the next marker.
        // [he*ll*o] --> [he*ll*][o]
        if (end < marker.length) {
            next = parent.insertBefore(text.splitText(end), next);
        }

        // Split out the text
        // [he*ll*][o] --> [he][ll][o]
        text = parent.insertBefore(text.splitText(start), next);

        // Create marker object from new text object
        var spawn = new Marker({
            index     : marker.index + 1,
            start     : (start = marker.start + start),
            end       : (end = marker.start + end),
            length    : end - start,
            text      : text,
            parent    : parent,
            textarea  : marker.textarea,
            before    : marker,
            after     : marker.after,
            br        : false,
            allowSpace: true,
            finalized : false
        });

        // Update current marker
        marker.end    = start,
        marker.length = marker.end - marker.start;
        marker.after  = spawn;

        return spawn;
    },

    clone: function() {

        return new Marker(
            $.pick(this, "index,start,end,length,text,parent,textarea,before,after,br,allowSpace,trigger,value,finalized".split(","))
        );
    },

    finalize: function(value) {

        var marker = this,
            block = marker.block;

        // Text marker cannot be finalized
        if (!block) return;

        var data = $(block).data("marker");
            data.value = value;
            data.finalized = true;

        $.extend(marker, data);
    }
});$.Controller("Mentions",
{
    pluginName: "mentions",
    hostname: "mentions",

    defaultOptions: {

        cssCloneProps: [
            'lineHeight', 'textDecoration', 'letterSpacing',
            'fontSize', 'fontFamily', 'fontStyle',
            'fontWeight', 'textTransform', 'textAlign',
            'direction', 'wordSpacing', 'fontSizeAdjust'
        ],

        triggers: {},

        inspector: false,

        "{textarea}": "[data-mentions-textarea]",
        "{overlay}" : "[data-mentions-overlay]",
        "{block}"   : "[data-mentions-overlay] > span"
    }
},
function(self){ return {

    init: function() {

        // Speed up access to overlay
        self._overlay  = self.overlay()[0];
        self._textarea = self.textarea()[0];

        self.cloneLayout();

        if (self.options.inspector) {
            self.inspect();
        }

        self.addPlugin("autocomplete");

        self.initialCaret = self.textarea().data("initial") || 0;
    },

    inspect: function() {
        self.inspector = self.addPlugin("inspector");
        self.inspector.showInspector();
    },

    setLayout: function() {

        self.normalize();
    },

    cloneLayout: function() {

        var textarea = self.textarea(),
            overlay  = self.overlay();

        $.each(self.options.cssCloneProps, function() {
            overlay.css(this, textarea.css(this));
        });

        overlay.css('opacity', 1);

        self.setLayout();
    },

    reset: function() {

        // Overlay
        var overlay = self.overlay(),
            overlayDefault = overlay.data("default");

        if (overlayDefault !== undefined) {
            // TODO: Use $.toHTML() in the future
            // after all is on 3.1.11.
            overlay.html($('<div>').html(overlayDefault).html());
        } else {
            overlay.empty();
        }

        // Textarea
        var textarea = self.textarea(),
            textareaDefault = textarea.data("default");

        if (textareaDefault !== undefined) {
            textarea.val($('<div>').html(textareaDefault).text());
        } else {
            textarea.val("");
        }

        self.caretBefore = self.caretAfter = {start: 0, end: 0};
        self.previousMarker = null;

        self.normalize();

        self.initialFocus = true;

        self.trigger("triggerClear");
    },

    //--- Triggers ----//

    getTrigger: function(key) {

        var triggers = self.options.triggers;
        if (triggers.hasOwnProperty(key)) {
            var trigger = triggers[key];
            trigger.key = key;
            return trigger;
        }
    },

    getStopIndex: function(str, stop) {

        var i = stop.length,
            idx = str.length;

        // Find the first earliest stop character, that's where the string ends
        while (i--) {
            var chr = stop.substr(i, 1),
                pos = str.indexOf(chr);
            idx = (pos < 0) ? idx : Math.min(idx, pos);
        }

        return idx;
    },

    //--- Marker traversal ----//

    getMarkers: function(callback) {

        var textarea = self._textarea,
            overlay = self._overlay,
            nodes = overlay.childNodes,
            node,
            i = 0,
            start = 0,
            before = null,
            skip = false,
            results = [],
            iterator = function(marker) {

                var ret;

                // Execute callback while passing in marker object
                if (callback) ret = callback.apply(marker, [marker]);

                // If callback returned:
                // false     - stop the loop
                // null      - don't add anything to the result list
                // undefined - add the same marker object to the result list
                // value     - add the value to the result list
                if (ret!==null && ret!==false) results.push(ret!==undefined ? ret : marker);

                return ret; // if ret is false, the parent loop will stop
            };

        while (node = nodes[i++]) {

            // Nodes
            var nodeType = node.nodeType,
                nodeName = node.nodeName,
                text, block = null,

                // Marker positions
                end, length,

                // Marker behaviour
                br = false, allowSpace = false;

            // If this is a text node, assign this node as marker text
            if (nodeType==3) {
                text = node;
                allowSpace = true;
            // else assign this node as marker block,
            // then test if node is <br/>, create a detached text node contaning a line break,
            } else if ((block = node) && nodeName=="BR") {
                text = document.createTextNode(_newline);
                br = true;
            // if this is an invalid node, e.g. node not element, node not span, span has no text child node,
            // remove code from overlay and skip this loop.
            } else if (nodeType!==1 || nodeName!=="SPAN" || !(text = node.childNodes[0]) || text.nodeType!==3) {
                overlay.removeChild(node);
                continue;
            }

            // Create marker props
            var props = {
                index     : i - 1,
                start     : start,
                end       : (end = start + (length = text.length)),
                length    : length,
                text      : text,
                block     : block,
                parent    : overlay,
                textarea  : textarea,
                before    : before,
                br        : br,
                allowSpace: allowSpace,
                finalized : false
            };

            // Create marker data
            if (block) {
                var $node = $(node), data = $node.data("marker");
                if (!data) (data = {}) && $node.data("marker", data);
                $.extend(props, data);
            }

            // Create marker
            var marker = new Marker(props);

            // If this is the second iteration, decorate the marker the after property
            // of the marker before this with the current marker.
            if (i > 1) {
                before.after = marker;
                // Execute iterator for the marker before this
                // If iterator returned false, stop the loop.
                if (skip = (iterator(before)===false)) break;
            }

            // Else reset start position and
            // continue with next child node.
            start = end;
            before = marker;
        }

        // Execute iterator one more time for the last marker
        if (!skip) iterator(before);

        return results;
    },

    getMarkerAt: function(pos) {

        if (pos===undefined) return;

        var marker;

        self.getMarkers(function(){

            // If position is inside current node,
            // stop and return marker.
            if (pos >= this.start && pos <= this.end) {
                marker = this;
                return false;
            }
        });

        return marker;
    },

    getMarkersBetween: function(start, end) {

        if (start===undefined) return;

        return self.getMarkers(function(){

            return (this.start > end) ? false : (this.end < start) ? null : this;
        });
    },

    toArray: function(stringify, asc) {

        var results = self.getMarkers(function(){

            var marker = this;

            if (!marker.block || marker.br) return null;

            // If there's no trigger, try to find it.
            if (!marker.trigger) {

                // Identify the trigger being used
                var wholeText = marker.text.nodeValue,
                    key = wholeText.slice(0, 1),
                    trigger = self.getTrigger(key);

                if (!trigger) return;

                marker.trigger = trigger;
                marker.value = wholeText.slice(1);
            }

            var data = {
                start  : marker.start,
                length : marker.length,
                type   : marker.trigger.type,
                value  : marker.value
            };

            return (stringify) ? JSON.stringify(data) : data;
        });

        return (asc) ? results : results.reverse();
    },

    //--- Marker/overlay/text manipulation ---//

    insert: function(str, start, end) {

        var marker, offset;

        // If we are inserting character(s)
        if (start===end || end===undefined) {

            // Get marker & offset
            marker = self.getMarkerAt(start);
            offset = marker.start;

            // Insert character
            marker.insert(str, start - offset, end - offset);

        } else {

            // If we are replacing character(s)

            // Identify affected markers
            var markers = self.getMarkersBetween(start, end),
                length = markers.length;

            // If there are no marker, stop.
            if (length < 1) return;

            // If we're modifying a single marker
            // e.g. he*llo* --> he*y*
            if (length==1) {

                // Get marker & offset
                marker = markers[0];
                offset = marker.start;

                // Insert character
                marker.insert(str, start - offset, end - offset);
            } else {

                // If we're modifying multiple markers
                // e.g. he*llo [john] [do*e] --> he*xxx*e

                // Deal with markers in reverse
                var i = length - 1,
                    marker = markers[i];

                // Convert block marker into text marker
                // [doe] --> doe
                // hello [john] [doe] --> hello [john] doe
                if (marker.block && end > marker.start) marker.toTextMarker();

                // Remove characters from text marker
                // doe --> e
                // hello [john] doe --> hello [john] e
                marker.insert("", 0, end - marker.start);

                // Remove all markers in between
                // [john] --> (removed)
                // hello [john] --> hello
                while ((marker = markers[--i]) && i > 0) {
                    marker.remove();
                }

                // If we're in the beginning of the textarea,
                // convert block into text marker.
                if (marker.block && marker.index===0 && start===0) marker.toTextMarker();

                // Insert characters in the first marker
                // hello -> hexxxe
                marker.insert(str, start - marker.start, marker.length);

                // Special case for handling br tag in the beginning of the textarea
                if (start===0 && marker.br) {
                    marker.remove();
                }
            }
        }

        // Normalize all text markers
        self.normalize();

        return marker;
    },

    textareaInsert: function(str, start, end) {

        var textarea = self._textarea,
            val = textarea.value;

        return textarea.value = val.substring(0, start) + str + val.slice(end);
    },

    normalize: function() {

        var overlay = self._overlay,
            textarea = self._textarea;

        // This clean up empty text nodes in the beginning and
        // the end of the overlay and join disjointed text nodes
        // that are supposed to be a single text node.
        overlay.normalize();

        // This is a double-edged workaround.
        // - When there is no child element (empty textarea),
        //   an empty text node ensure overlay has a minimum
        //   single line-height.
        // - If there is a newline at the end of the overlay,
        //   an empty text node ensure overlay accomodates
        //   the height of the newline.
        var last = overlay.lastChild;

        if (!last || last.nodeName==="BR") {
            overlay.appendChild(document.createTextNode(""));
        }

        // Chrome, Opera & IE doesn't accomodate height of
        // newline after an empty text node, so reset the
        // overlay height to auto, and retrieve the textarea
        // scrollHeight again.
        overlay.style.height = "auto";
        overlay.style.height = textarea.scrollHeight + "px";

        // IE & Opera textarea's scrollTop may jump position
        // from time to time so we need to reset it back.
        textarea.scrollTop = 0;

        // Remember the current textarea length.
        // We do it here instead of keydown event
        // because Opera returns the length of the
        // textarea after it has been changed.
        self.lengthBefore = textarea.value.length;

        // console.log("after", overlay.childNodes);
    },

    //--- Key events & caret handling ---//

    /*
    List of input patterns to test:

    0. Meta-characters via alt + shift + (any key).

    1. Holding arrow key + pressing another character.

    2. Select a range of characters (covering single/multiple marker)
       - and press any key
       - and press enter
       - and press backspace

    3. Repeat step 2 with range starting at a block marker where caret is at:
       - the beginning
       - the middle
       - the end
       of the block marker and also when block marker is at:
       - the beginning
       - the middle
       - the end
       of the textarea.

    4. Typing accented character.
       Hold a key until candidate window shows up, then:
       - Press a number
       - Release key, then press a number
       - Navigate using arrow keys
       - Press enter to select a character
       - Click on a candidate to select a character
       - Press backspace until candidate window dissappears

    5. Typing romanized-to-unicode (Chinese/Japanese/Arabian/etc) characters.
       Type multiple characters in the candidate window, then proceed with
       the next course of action at test no. 4.

    6. Pressing enter continously to create multiple newlines:
       - at the beginning of the textarea
       - at the middle of marker/text
       - at the end of textarea
       then:
       - enter a key at the newline
       - press backspace to remove those newlines
       - select a range of newlines, then proceed with
         the next course of action at test no. 2.
    */

    lengthBefore: 0,
    caretBefore: {start: 0, end: 0},
    caretAfter: {start: 0, end: 0},
    skipKeydown: false,
    previousMarker: null,

    initialFocus: true,

    "{textarea} focus": function() {

        if (self.initialFocus) {
            self.textarea().caret(self.initialCaret || 0);
        }
    },

    "{textarea} keydown": function(textarea, event) {

        self.initialFocus = false;

        // If keydown event has been fired multiple times
        // this might mean the user has entered candidate
        // window and we should not do anything.
        if (self.skipKeydown) return;

        var caret = self.caretBefore = textarea.caret();

        if (event.keyCode===8 && $.IE < 10) {
            self.overlay().css('opacity', 0);
        }

        // console.log("keydown", event.which, caret);

        self.skipKeydown = true;
    },

    // Keypress event will not trigger when meta keys are pressed,
    // it will trigger on well-formed characters.
    "{textarea} keypress": function(textarea, event) {

        // console.log("keypress");

        // This will help on situations where user
        // holds an arrow key + presses another character.
        self.caretBefore = textarea.caret();

        // FF fires keypress on backspace, while Chrome & IE doesn't.
        // We normalize this behaviour by not doing anything on backspace.
        if (event.keyCode===8) return;
    },

    "{textarea} input": function(textarea) {

        self.reflect();

        // Extra precaution in case overlay goes wrong,
        // user can start all over again by reseting mentions.
        if (textarea.val().length < 1) {
            self.reset();
        }
    },

    "{textarea} keyup": function(textarea, event) {

        self.skipKeydown = false;

        // Listen to backspace during keydown because
        // it is not fired on input/keypress on IE9.
        if (event.keyCode===8 && $.IE < 10) {

            var caretBefore = self.caretBefore,
                caretAfter  = self.caretAfter = self.textarea().caret();

            self.insert("", caretAfter.end, caretBefore.end);

            self.caretBefore = caretAfter;

            self.overlay().css('opacity', 1);
        }

        // console.log("keyup", caretBefore, caretAfter);
    },

    reflect: function() {

        var textarea = self._textarea,

            wholeText = textarea.value,

            // Caret position retrieved on previous input event
            // is the position before the character is inserted
            caretBefore = self.caretBefore,

            // Caret position retrieved on current input event
            // is the position after the character is inserted.
            caretAfter = self.caretAfter = $(textarea).caret(),

            // Determine if user is on Opera + candidate window.
            operaCandidateWindow = ($.browser.opera && caretAfter.end > caretAfter.start),

            marker = self.getMarkerAt(caretBefore.start),

            diff = self.lengthBefore - wholeText.length,

            replace = false;

        // Ensure Opera follows the caretBefore behaviour of other
        // browsers when typing inside the candidate window.
        if (operaCandidateWindow) {
            if (caretBefore.start!==caretBefore.end) {
                caretBefore.end += diff;
            }
        }

        // In case there was an issue retrieving marker.
        // TODO: Figure out the pattern, usually when typed too early.
        if (!marker) return;

        var previousMarker = self.previousMarker,
            block = marker.block;

        if (previousMarker) {

            var previousBlock = previousMarker.block,
                finalize = (previousMarker.trigger || {}).finalize;

            // TODO: Make this less temperamental.
            if (previousBlock && finalize && !previousBlock.finalized && previousBlock!==block) {
                try {
                    previousMarker.toTextMarker();
                } catch(e) {
                    self.previousMarker = null;
                }
            }
        }

        // console.log("caretBefore", caretBefore.start, caretBefore.end);
        // console.log("caretAfter" , caretAfter.start , caretAfter.end);

        // If there is a change in the text content but the length of the
        // text content is the same length as before, it is impossible to
        // tell what has changed, so we replace the entire text in the marker
        // where the caret is at. This happens when:
        // - User holds a character + presses a number to select a
        //   character from the candidate window.
        // - User navigates between characters using arrow keys
        //   within the candidate window.
        //
        // The caretAfter could be earlier than the caretBefore when:
        // - User enters backspace to remove a character.
        // - User finalizes a selection from the candidate window where characters
        //   are shorter than being typed, e.g. "ni hao" --> "你好".
        if (!marker.br && (diff===0 || caretAfter.end < caretBefore.start)) {

            var textStart  = marker.start,
                textEnd    = marker.end - diff,
                rangeStart = caretAfter.end,
                rangeEnd   = caretBefore.start,
                replace    = textStart!==textEnd;

         // If user is inserting text as usual.
        } else {

            // In Chrome, the caretAfter has a range if the user is typing within the
            // candidate window. The characters may change due to fuzzy logic suggestions.
            // You can test this by using Chinese pinyin input and typing "a" then
            // "asdasdasd" one at a time slowly until you see the difference.

            // So, we give prefential treatment to start positions which are earlier
            // whether it is coming from caretBefore or caretAfter.
            var rangeStart = textStart = Math.min(caretBefore.start, caretAfter.start),
                rangeEnd   = caretBefore.end,
                textEnd    = caretAfter.end;
        }

        // Extract text from the given start and end position
        var text = wholeText.substring(textStart, textEnd);

        // If the strategy is to replace a single marker
        if (replace) {

            // If text being replaced is not identical on
            // a finalized marker, then convert to text marker.
            if (marker.val()!==text && marker.finalized) {
                marker.toTextMarker();
            }

            marker.val(text);

            // Emulate markerInsert event
            self.overlay().trigger("markerInsert", marker, [], text, textStart, textEnd);

            self.normalize();

        // If the strategy is to insert chracters onto single/multiple markers
        } else {
            self.insert(text, rangeStart, rangeEnd);
        }

        // console.log("range", rangeStart, rangeEnd);
        // console.log("text" , textStart, textEnd, text);

        // Ensure Opera follows the caretAfter behaviour of other
        // browsers when typing inside the candidate window.
        if (operaCandidateWindow) {
            caretAfter.start = caretAfter.end;
        }

        // Set caretBefore as current caret
        // This is used to track text range when exiting candidate window.
        self.caretBefore = self.caretAfter;
    },

    //--- Marker Events ----//

    "{overlay} markerInsert": function(overlay, event, marker, nodes, str, start, end) {

        var text = marker.text,
            wholeText = text.nodeValue,
            trigger;

        self.previousMarker = null;

        // If a trigger key was entered
        if (trigger = self.getTrigger(str)) {

            // Ensure the character before is a space, e.g.
            // we don't want to listen to @ in an email address.
            // or a # that is not intended to be a hashtag.
            var charBefore = wholeText.charCodeAt(start - 1),
                brBefore = marker.before && marker.before.br;

            if (marker.index===0 || (charBefore===32 || brBefore)) {

                // Extract the remaining string after the trigger key
                // coding #js --> #js
                var remainingText = wholeText.slice(start),
                    content = remainingText.slice(1);

                // If this trigger allows wrapping and
                // there are remaining characters to wrap.
                // *#js and*    --> *#js* and
                // *#js#foobar* --> *#js*#foobar
                if (trigger.wrap && remainingText.length > 1) {

                    // Get stop position, add start offset and trigger key offset.
                    end = self.getStopIndex(content, trigger.stop) + start + 1;

                // If trigger does not allow wrapping
                // *@foobar* --> *@*foobar
                } else {
                    end = start + 1;
                }

                // Spawn a new marker from this string
                // and convert this marker into a block marker
                // *#*          --> [#]
                // *#js* and    --> [#js] and
                // *#js*#foobar --> [#js]#foobar
                // *@*foobar    --> [@]foobar
                var spawn = marker.spawn(start, end).toBlockMarker(),
                    content = spawn.val().slice(1);

                // Update data
                var data = $(spawn.block).data("marker");
                    data.value = content;
                    data.trigger = trigger;

                self.previousMarker = spawn;

                // Trigger triggerCreate event
                self.trigger("triggerCreate", [spawn, trigger, content]);
            }
        }

        // If we're inside an existing block marker,
        // determine if we need to mutate the block.
        if (marker.block && !marker.br) {

            // If this marker is finalized, any changes to the
            // text content will convert it to a text marker.
            // [Jensen *#*Tonne] --> Jensen #Tonne
            // [Jensen Tonn`e`]  --> Jensen Tonn
            if (marker.finalized) {

                var length = marker.length;

                if (end < length - 1) marker.toTextMarker();

            } else {

                // Identify the trigger being used
                var key = wholeText.slice(0, 1),
                    trigger = self.getTrigger(key);

                // If we could not identify the trigger, skip.
                if (!trigger) return;

                // Check for occurence of stop character
                var content = wholeText.slice(1),
                    start = self.getStopIndex(content, trigger.stop) + 1,
                    end = wholeText.length,
                    spawn = false;

                // If the end position is shorter than content length
                if (start < end) {

                    // Spawn out a new marker containing
                    // the remaining text after the block marker.
                    // [#foo* *bar] --> [#foo] bar
                    spawn = marker.spawn(start, end);
                }

                // Trigger triggerChange event
                content = marker.val().slice(1);

                // Update data
                var data = $(marker.block).data("marker");
                    data.value = content;
                    data.trigger = trigger;

                self.previousMarker = marker;

                self.trigger("triggerChange", [marker, spawn, trigger, content]);
            }
        }
    }

    // Events available for use
    // "{overlay} markerRemove": function(overlay, event, marker) {},
    // "{overlay} markerConvert": function(overlay, event, marker, type) {},
    // "{self} triggerCreate": function(el, event, marker, trigger, content) {},
    // "{self} triggerDestroy": function(el, event, marker) {},
    // "{self} triggerChange": function(el, event, marker, spawn, trigger) {},

    // TODO: Better support for cut & paste
    // "{textarea} beforecut": function() { console.log("BEFORECUT", arguments); },
    // "{textarea} beforepaste": function() { console.log("BEFOREPASTE", arguments); },
    // "{textarea} cut": function(el, event) { console.log("CUT", arguments); },
    // "{textarea} paste": function() { console.log("PASTE", arguments); }
}});
$.template("mentions/menu", '<div class="mentions-autocomplete" data-mentions-autocomplete><b><b></b></b><div class="mentions-autocomplete-inner" data-mentions-autocomplete-viewport><div class="mentions-autocomplete-loading" data-mentions-autocomplete-loading data-mentions-autocomplete-close></div><div class="mentions-autocomplete-empty" data-mentions-autocomplete-empty></div><div class="mentions-autocomplete-search" data-mentions-autocomplete-search></div><ul class="mentions-menu" data-mentions-menu></ul></div></div>');
$.template("mentions/menuItem", '<li class="mentions-menuItem" data-mentions-menuItem>[%== html %]</li>');
$.template("mentions/loadingHint", '<i class="mentions-autocomplete-loading-indicator"></i>');
$.template("mentions/searchHint", '<span class="mentions-autocomplete-search-hint">Type a keyword to begin.</span>');
$.template("mentions/emptyHint", '<span class="mentions-autocomplete-empty-text">No items found.</span>');
/*
<div class="mentions-autocomplete" data-mentions-autocomplete>
	<b><b></b></b>
	<div class="mentions-autocomplete-inner" data-mentions-autocomplete-viewport>
		<div class="mentions-autocomplete-loading" data-mentions-autocomplete-loading></div>
		<div class="mentions-autocomplete-empty" data-mentions-autocomplete-empty></div>
		<div class="mentions-autocomplete-search" data-mentions-autocomplete-search></div>
		<ul class="mentions-menu" data-mentions-menu></ul>
	</div>
</div>
*/

$.Controller("Mentions.Autocomplete",
{
    defaultOptions: {

		view: {
			menu: "mentions/menu",
			menuItem: "mentions/menuItem",
			searchHint: "mentions/searchHint",
			loadingHint: "mentions/loadingHint",
			emptyHint: "mentions/emptyHint"
		},

		id: "",

		// This is the default query options
		// applied to all triggers unless
		// trigger override them.
		query: {
			data: null,
			cache: true,
			minLength: 1,
			limit: 10,
			highlight: true,
			caseSensitive: false,
			exclusive: false,
			searchHint: false,
			loadingHint: false,
			emptyHint: false
		},

		position: {
			my: 'left top',
			at: 'left bottom',
			collision: 'none'
		},

        size: {
            width: "auto",
            height: "auto"
        },

		"{menu}": "[data-mentions-menu]",
		"{menuItem}": "[data-mentions-menuItem]",
		"{viewport}": "[data-mentions-autocomplete-viewport]",
		"{loadingHint}": "[data-mentions-autocomplete-loading]",
		"{emptyHint}": "[data-mentions-autocomplete-empty]",
		"{searchHint}": "[data-mentions-autocomplete-search]",
		"{closeButton}": "[data-mentions-autocomplete-close]"
    }
},
function(self){ return {

    init: function() {

		// Destroy controller
		if (!self.element.data(self.Class.fullName)) {

			self.destroy();

			// And reimplement on the context menu we created ourselves
			self.view.menu()
				.appendTo("body")
				.attr("id", self.options.id)
				.data(self.Class.fullName, true)
				.addController(self.Class, self.options);

			return;
		}

		var mentions = self.mentions;

		mentions.autocomplete = self;
		mentions.pluginInstances["autocomplete"] = self;

		// Set the position to be relative to the mentions
		if (!self.options.position.of) {
			self.options.position.of = self.mentions.element;
		}

		// Prepare this in advance to speed things up
		self.defaultSearchHint  = self.view.searchHint().toHTML();
		self.defaultEmptyHint   = self.view.emptyHint().toHTML();
		self.defaultLoadingHint = self.view.loadingHint().toHTML();

		// Only reattach element when autocomplete is needed.
		self.element.detach();
    },

	setLayout: function() {

		if (!self.hidden) {

            var options = self.options,
                size = options.size,
                width = self.mentions.element.outerWidth(),
                height = "auto";

            if ($.isFunction(size.width)) {
                width = size.width(width);
            }

            if ($.isFunction(size.height)) {
                height = size.height(height);
            }

			self.element
				.css({
					opacity: 1,
					width: width
				})
				.position(self.options.position);

			setTimeout(function(){
				self.viewport()
					.addClass("active");
			}, 1);
		}
	},

	"{window} resize": $.debounce(function() {
		self.element.css("opacity", 0);
		self.setLayout();
	}, 250),

	"{window} scroll": $.debounce(function() {
		self.element.css("opacity", 0);
		self.setLayout();
	}, 250),

	"{window} dialogTransitionStart": function() {
		self.hidden = true;
		self.element.css("opacity", 0);
	},

	"{window} dialogTransitionEnd": function() {
		self.hidden = false;
		self.setLayout();
	},

	currentMarker: null,

	"{mentions} triggerCreate": function(el, event, marker, trigger, content) {

		self.populate(marker, trigger, content);

		self.currentMarker = marker;
	},

	"{mentions} triggerChange": function(el, event, marker, spawn, trigger, content) {

		self.populate(marker, trigger, content);

		self.currentMarker = marker;
	},

	"{mentions.block} triggerDestroy": function(el, event, marker) {

		self.hide();
	},

	"{mentions} triggerClear": function() {

		self.hide();
	},

	hidden: true,

	show: function(duration) {

		clearTimeout(self.sleep);

		self.element
			.appendTo("body")
			.show();

		self.hidden = false;

		self.viewport().removeClass("active");

		self.setLayout();

		if (duration) {

			self.sleep = setTimeout(function(){

				self.hide();

			}, duration);
		}
	},

	hide: function() {

		self.element.hide();

		var menuItem = self.menuItem(),
			activeMenuItem = menuItem.filter(".active");

		if (activeMenuItem.length > 0) {
			self.lastItem = {
				// keyword: $.trim(self.textboxlist.textField().val()),
				keyword: "", // TODO: Port this
				item   : activeMenuItem.data("item")
			};
		}

		self.viewport().removeClass("active");

		menuItem.removeClass("active");

		self.render.reset();

		self.hidden = true;

		// Clear any previous sleep timer first
		clearTimeout(self.sleep);

		// If no activity within 3000 seconds, detach myself.
		self.sleep = setTimeout(function(){
			self.element.detach();
		}, 3000);
	},

	query: function(options) {

		if (!options) return;

		// If options passed in is not an object
		var query = $.extend(
				{},
				self.options.query,
				($.isPlainObject(options) ? options : {data: options})
			),
			data = query.data;

		if (!data) return;

		// Query URL
		if ($.isUrl(data)) {
			var url = data;
			query.lookup = function() {
				return $.ajax(url + query.keyword);
			}
		}

		// Query function
		if ($.isFunction(data)) {
			var func = data;
			query.lookup = function() {
				return func.call(self, query.keyword);
			}
		}

		// Query dataset
		if ($.isArray(data)) {

			var dataset = data;
			query.lookup = function() {

				var task = $.Deferred(),
					keyword = query.keyword.toLowerCase();

				// Fork this process
				// so it won't choke on large dataset.
				setTimeout(function(){
					var result = $.grep(dataset, function(item){
						return item.title.toLowerCase().indexOf(keyword) > -1;
					});
					task.resolve(result);
				}, 0);

				return task;
			}
		}

		return query;
	},

	tasks: {},

	delayTask: null,

	activeQuery: null,

	populate: function(marker, trigger, keyword) {

		// Create query object
		var query = self.query(trigger.query);

		if (!query) return;

		// Set current query as active query
		self.activeQuery = query;

		// Store data in query
		query.keyword = keyword;
		query.trigger = trigger;
		query.marker  = marker;

		// Trigger queryPrepare event
		// for event handlers to modify the query object.
		self.trigger("queryPrepare", [query]);

		// If no keyword given or keyword doesn't meet minimum query length, stop.
		var keyword = query.keyword;

		if (keyword==="" || (keyword.length < query.minLength)) {

			var searchHint = query.searchHint;

			if (searchHint) {

				self.searchHint()
					.html(
						// If searchHint is a html string
						$.isString(searchHint) ?
							// use query-specific searchHint
							searchHint :
							// else use default searchHint
							self.defaultSearchHint
					);

				self.element.addClass("search");

				self.show();
			} else {
				self.hide();
			}
			return;
		}

		// Create a query id for this task based on the keyword
		// and retrieve existing query task for this keyword.
		var id    = query.id = trigger.key + "|" + (query.caseSensitive) ? keyword : keyword.toLowerCase(),
			tasks = self.tasks,
			task  = query.task = tasks[id],

			// Determine if this is a new or existing query task
			// If query caching is disabled, it will always be a new task.
			newTask = !$.isDeferred(task) || !query.cache,

			// This function runs the query task
			// We wrap it in a function because we may
			// want to debounce running of this task.
			runTask = function(){

				// Trigger keywordBeforeQuery event
				// If the event was prevented, don't query the keyword.
				var event = self.trigger("queryBeforeStart", [query]);
				if (event.isDefaultPrevented()) return;

				// Query the keyword if:
				// - The query hasn't been made.
				// - The query has been rejected.
				if (newTask || (!newTask && task.state()=="rejected")) {
					task = tasks[id] = query.task = query.lookup();
				}

				// When query lookup is done, render items;
				task.done(
					self.render(function(items){
						return [items, query];
					})
				);

				// Trigger query event
				self.trigger("queryStart", [query]);
			};

		// If this is a new query task
		// Don't run until we are sure that user has finished typing
		if (newTask) {

			clearTimeout(self.delayTask);
			self.delayTask = setTimeout(runTask, 250);

		// Else run it immediately
		} else {
			runTask();
		}
	},

	"{self} queryPrepare": function(el, event, query) {

		// Remove both loading & empty class
		el.removeClass("loading empty search");

		if (query.loadingHint) {
			self.hide();
		}
	},

	"{self} queryBeforeStart": function(el, event, query) {

		var loadingHint = query.loadingHint;

		// Show loading hint
		if (loadingHint) {

			self.loadingHint()
				.html(
					// If searchHint is a html string
					$.isString(loadingHint) ?
						// use query-specific loadingHint
						loadingHint :
						// else use default loadingHint
						self.defaultLoadingHint
				);

			el.addClass("loading");
			self.show();
		}
	},

	"{self} queryStart": function(el, event, query) {

		query.task
			.fail(function(){
				self.hide();
			})
			.always(function(){
				el.removeClass("loading");
			});
	},

	render: $.Enqueue(function(items, query){

		// If items passed in isn't an array,
		// fake an empty array.
		if (!$.isArray(items)) { items = [] };

		// Get mentions
		var mentions = self.mentions,
			autocomplete = self,
			element = self.element,
			menu = self.menu(),
			keyword = query.keyword;

		// If there are no items, hide menu.
		if (items.length < 1) {

			var emptyHint = query.emptyHint;

			// If we are supposed to show an empty hint
			if (emptyHint) {

				self.emptyHint()
					.html(
						// If searchHint is a html string
						$.isString(emptyHint) ?
							// use query-specific emptyHint
							emptyHint :
							// else use default emptyHint
							self.defaultEmptyHint
					);

				// Clear out menu
				menu.empty();

				// Add empty class
				element.addClass("empty");

				// Show menu for only 2 seconds
				self.show(2000);

			// Just hide straight away
			} else {

				self.hide();
			}

			// Trigger menuRender event
			mentions.trigger("menuRender", [menu, query, autocomplete, mentions]);

			return;
		}

		// Remove empty class
		element.removeClass("empty");

		// Generate menu items
		if (!query.cache || menu.data("keyword")!==keyword) {

			// Clear out menu items
			menu.empty();

			$.each(items, function(i, item){

				// Trigger menuCreateItem
				mentions.trigger("menuCreateItem", [item, query, autocomplete, mentions]);

				// If the item is not an object,
				// or item should be discarded, stop.
				if (!$.isPlainObject(item) || item.discard) return;

				var html = item.menuHtml || item.title;

				self.view.menuItem({html: html})
					.data("item", item)
					.appendTo(menu);
			});

			menu.data("keyword", keyword);
		}

		// Get menu Items
		var menuItems = self.menuItem();

		// Trigger menuCreate event
		mentions.trigger("menuCreate", [menu, menuItems, query, autocomplete, mentions]);

		// If menu is empty, toggle empty classname
		if (menuItems.filter(":not(.hidden)").length < 1) {

			element.addClass("empty");

			// If we shouldn't show an empty hint
			if (!query.emptyHint) {

				// Hide menu straightaway
				return self.hide();
			}
		}

		// If we only allow adding item from suggestions
		if (query.exclusive) {

			// Automatically select the first item
			self.menuItem(":not(.hidden):first").addClass("active");
		}

		// Trigger renderMenu event
		mentions.trigger("renderMenu", [menu, query, autocomplete, mentions]);

		self.show();
	}),

	"{mentions.textarea} keydown": function(textarea, event) {

		// Prevent autocomplete from falling asleep.
		clearTimeout(self.sleep);

		// Get active menu item
		var activeMenuItem = self.menuItem(".active:not(.hidden)");

		if (activeMenuItem.length < 1) {
			activeMenuItem = false;
		}

		switch (event.keyCode) {

			// If up key is pressed
			case KEYCODE.UP:

				// Deactivate all menu item
				self.menuItem().removeClass("active");

				// If no menu items are activated,
				if (!activeMenuItem) {

					// activate the last one.
					self.menuItem(":not(.hidden):last").addClass("active");

				// Else find the menu item before it,
				} else {

					// and activate it.
					activeMenuItem.prev(self.menuItem.selector + ':not(.hidden)')
						.addClass("active");
				}

				// Prevent up/down keys from changing textfield cursor position.
				event.preventDefault();
				break;

			// If down key is pressed
			case KEYCODE.DOWN:

				// Deactivate all menu item
				self.menuItem().removeClass("active");

				// If no menu items are activated,
				if (!activeMenuItem) {

					// activate the first one.
					self.menuItem(":not(.hidden):first").addClass("active");

				// Else find the menu item after it,
				} else {

					// and activate it.
					activeMenuItem.next(self.menuItem.selector + ':not(.hidden)')
						.addClass("active");
				}

				// Prevent up/down keys from changing textfield cursor position.
				event.preventDefault();
				break;

			// If escape is pressed,
			case KEYCODE.ESCAPE:

				// hide menu.
				self.hide();
				break;

			// If enter is pressed, use item
			case KEYCODE.ENTER:

				if (!self.hidden && activeMenuItem) {
					var item = activeMenuItem.data("item");
					self.use(item);
					event.preventDefault();
				}
				break;
		}

		// Get newly activated item
		var activeMenuItem = self.menuItem(".active:not(.hidden)");

		if (!self.hidden) {
			// Scroll menu viewport if it is out of visible area.
			self.viewport().scrollIntoView(activeMenuItem);
		}
	},

	"{menuItem} mouseup": function(menuItem) {

		// Hide context menu
		self.hide();

		// Add item
		var item = menuItem.data("item");

		self.use(item);

		// Refocus textarea
		setTimeout(function(){

			// Due to event delegation, this needs to be slightly delayed.
			self.mentions.textarea().focus();
		}, 150);
	},

	use: function(item) {

		// Get active query
		var query = self.activeQuery;

		// If there are no active query, stop.
		if (!query) return;

		var marker = query.marker,
			title = item.title;

		// Replace marker text
		marker.text.nodeValue = title;

		delete item["menuHtml"];

		// Finalize marker
		marker.finalize(item);

		// Replace textarea text
		self.mentions.textareaInsert(title, marker.start, marker.end);

		// Set caret position
		self.mentions.textarea().caret(marker.start + title.length);

		// Quick hack to prevent repopulation
		self.hidden = true;

		self.hide();
	},

	"{menuItem} mouseover": function(menuItem) {

		self.menuItem().removeClass("active");

		menuItem.addClass("active");
	},

	"{menuItem} mouseout": function(menuItem) {

		self.menuItem().removeClass("active");
	},

	"{closeButton} click": function() {

		self.hide();
	},

	"{mentions} destroyed": function() {

		self.element.remove();
	}
}});
$.template("mentions/inspector", '<div class="mentions-inspector" data-mentions-inspector><fieldset><b>Selection</b><hr/><label>Start</label><input type="text" data-mentions-selection-start/><hr/><label>End</label><input type="text" data-mentions-selection-end/><hr/><label>Length</label><input type="text" data-mentions-selection-length/><hr/></fieldset><fieldset><b>Trigger</b><hr/><label>Key</label><input type="text" data-mentions-trigger-key/><hr/><label>Type</label><input type="text" data-mentions-trigger-type/><hr/><label>Buffer</label><input type="text" data-mentions-trigger-buffer/><hr/></fieldset><hr/> <fieldset><b>Marker</b><hr/><label>Index</label><input type="text" data-mentions-marker-index/><hr/><label>Start</label><input type="text" data-mentions-marker-start/><hr/><label>End</label><input type="text" data-mentions-marker-end/><hr/><label>Length</label><input type="text" data-mentions-marker-length/><hr/><label>Text</label><input type="text" data-mentions-marker-text/><hr/></fieldset><fieldset><b>Block</b><hr/><label>Html</label><input type="text" data-mentions-block-html/><hr/><label>Text</label><input type="text" data-mentions-block-text/><hr/><label>Type</label><input type="text" data-mentions-block-type/><hr/><label>Value</label><input type="text" data-mentions-block-value/><hr/></fieldset></div>');

/*
<div class="mentions-inspector" data-mentions-inspector>
    <fieldset>
        <b>Selection</b>
        <hr/>
        <label>Start</label>
        <input type="text" data-mentions-selection-start/>
        <hr/>
        <label>End</label>
        <input type="text" data-mentions-selection-end/>
        <hr/>
        <label>Length</label>
        <input type="text" data-mentions-selection-length/>
        <hr/>
    </fieldset>
    <fieldset>
        <b>Trigger</b>
        <hr/>
        <label>Key</label>
        <input type="text" data-mentions-trigger-key/>
        <hr/>
        <label>Type</label>
        <input type="text" data-mentions-trigger-type/>
        <hr/>
        <label>Buffer</label>
        <input type="text" data-mentions-trigger-buffer/>
        <hr/>
    </fieldset>
    <hr/> 
    <fieldset>
        <b>Marker</b>
        <hr/>
        <label>Index</label>
        <input type="text" data-mentions-marker-index/>
        <hr/>
        <label>Start</label>
        <input type="text" data-mentions-marker-start/>
        <hr/>
        <label>End</label>
        <input type="text" data-mentions-marker-end/>
        <hr/>
        <label>Length</label>
        <input type="text" data-mentions-marker-length/>
        <hr/>
        <label>Text</label>
        <input type="text" data-mentions-marker-text/>
        <hr/>
    </fieldset>
    <fieldset>
        <b>Block</b>
        <hr/>
        <label>Html</label>
        <input type="text" data-mentions-block-html/>
        <hr/>
        <label>Text</label>
        <input type="text" data-mentions-block-text/>
        <hr/>
        <label>Type</label>
        <input type="text" data-mentions-block-type/>
        <hr/>
        <label>Value</label>
        <input type="text" data-mentions-block-value/>
        <hr/>
    </fieldset>
</div>
*/

$.Controller("Mentions.Inspector",
{
    defaultOptions: {

        view: {
            item: "mentions/item",
            inspector: "mentions/inspector"
        },

        "{inspector}": "[data-mentions-inspector]",

        "{selectionStart}" : "[data-mentions-selection-start]",
        "{selectionEnd}"   : "[data-mentions-selection-end]",
        "{selectionLength}": "[data-mentions-selection-length]",

        "{markerIndex}" : "[data-mentions-marker-index]",
        "{markerStart}" : "[data-mentions-marker-start]",
        "{markerEnd}"   : "[data-mentions-marker-end]",
        "{markerLength}": "[data-mentions-marker-length]",
        "{markerText}"  : "[data-mentions-marker-text]",

        "{blockText}" : "[data-mentions-block-text]",
        "{blockHtml}" : "[data-mentions-block-html]",
        "{blockType}" : "[data-mentions-block-type]",
        "{blockValue}": "[data-mentions-block-value]",

        "{triggerKey}" : "[data-mentions-trigger-key]",
        "{triggerType}" : "[data-mentions-trigger-type]",
        "{triggerBuffer}" : "[data-mentions-trigger-buffer]"        
    }
},
function(self){ return {

    init: function() {
    },

    showInspector: function() {

        // If inspector hasn't been created yet
        if (self.inspector().length < 1) {

            // Create inspector and append to textfield
            self.view.inspector()
                .appendTo(self.element);
        }

        self.inspector().show();

        self.mentions.overlay().css("color", "green");
    },

    hideInspector: function() {

        self.inspector().hide();

        self.mentions.overlay().css("color", "transparent");
    },

    "{mentions.textarea} keyup": function() {

        self.inspect();
    },

    "{inspector} dblclick": function() {

        self.textarea().toggle();
    },

    inspect: $.debounce(function() {

        // Selection
        var caret = self.mentions.textarea().caret();

        self.selectionStart().val(caret.start);
        self.selectionEnd().val(caret.end);
        self.selectionLength().val(caret.end - caret.start);

        // Trigger
        var triggerKey = self.triggered;

        if (triggerKey) {
            var trigger = self.getTrigger(triggerKey);
            self.triggerKey().val(triggerKey);
            self.triggerType().val(trigger.type);
            self.triggerBuffer().val(self.buffer);
        } else {
            self.triggerKey().val('');
            self.triggerType().val('');
            self.triggerBuffer().val('');
        }

        // Marker
        var marker = self.mentions.getMarkerAt(caret.start);

        if (marker) {
            self.markerIndex().val(marker.index).data('marker', marker);
            self.markerStart().val(marker.start);
            self.markerEnd().val(marker.end);
            self.markerLength().val(marker.length);
            self.markerText().val(marker.text.nodeValue);
        } else {
            self.markerIndex().val('').data('marker', null);
            self.markerStart().val('');
            self.markerEnd().val('');
            self.markerLength().val('');
            self.markerText().val('');
        }

        // Block
        var block = (marker || {}).block;

        if (block) {
            self.blockText().val(marker.text.nodeValue);
            self.blockHtml().val($(block).clone().toHTML());
            // TODO: Retrieve block type & value 
        } else {
            self.blockText().val('');
            self.blockHtml().val('');
        }

    }, 25),

    "{markerIndex} click": function(el) {
        console.dir(el.data("marker"));
    }

}});

}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("mentions", moduleFactory);

}());