ed.define('edjquery', function() {

    var $ = window.jQuery;

    // duplicate buildFragment from media/com_easydiscuss/scripts/vendors/edjquery.js
    $.buildFragment = function( elems, context, scripts, selection ) {
        var j, elem, contains,
            tmp, tag, tbody, wrap,
            l = elems.length,

            // Ensure a safe fragment
            safe = createSafeFragment( context ),

            nodes = [],
            i = 0;

        for ( ; i < l; i++ ) {
            elem = elems[ i ];

            if ( elem || elem === 0 ) {

                // Add nodes directly
                if ( jQuery.type( elem ) === "object" ) {
                    jQuery.merge( nodes, elem.nodeType ? [ elem ] : elem );

                // Convert non-html into a text node
                } else if ( !rhtml.test( elem ) ) {
                    nodes.push( context.createTextNode( elem ) );

                // Convert html into DOM nodes
                } else {
                    tmp = tmp || safe.appendChild( context.createElement("div") );

                    // Deserialize a standard representation
                    tag = (rtagName.exec( elem ) || [ "", "" ])[ 1 ].toLowerCase();
                    wrap = wrapMap[ tag ] || wrapMap._default;

                    tmp.innerHTML = wrap[1] + elem.replace( rxhtmlTag, "<$1></$2>" ) + wrap[2];

                    // Descend through wrappers to the right content
                    j = wrap[0];
                    while ( j-- ) {
                        tmp = tmp.lastChild;
                    }

                    // Manually add leading whitespace removed by IE
                    if ( !support.leadingWhitespace && rleadingWhitespace.test( elem ) ) {
                        nodes.push( context.createTextNode( rleadingWhitespace.exec( elem )[0] ) );
                    }

                    // Remove IE's autoinserted <tbody> from table fragments
                    if ( !support.tbody ) {

                        // String was a <table>, *may* have spurious <tbody>
                        elem = tag === "table" && !rtbody.test( elem ) ?
                            tmp.firstChild :

                            // String was a bare <thead> or <tfoot>
                            wrap[1] === "<table>" && !rtbody.test( elem ) ?
                                tmp :
                                0;

                        j = elem && elem.childNodes.length;
                        while ( j-- ) {
                            if ( jQuery.nodeName( (tbody = elem.childNodes[j]), "tbody" ) && !tbody.childNodes.length ) {
                                elem.removeChild( tbody );
                            }
                        }
                    }

                    jQuery.merge( nodes, tmp.childNodes );

                    // Fix #12392 for WebKit and IE > 9
                    tmp.textContent = "";

                    // Fix #12392 for oldIE
                    while ( tmp.firstChild ) {
                        tmp.removeChild( tmp.firstChild );
                    }

                    // Remember the top-level container for proper cleanup
                    tmp = safe.lastChild;
                }
            }
        }

        // Fix #11356: Clear elements from fragment
        if ( tmp ) {
            safe.removeChild( tmp );
        }

        // Reset defaultChecked for any radios and checkboxes
        // about to be appended to the DOM in IE 6/7 (#8060)
        if ( !support.appendChecked ) {
            jQuery.grep( getAll( nodes, "input" ), fixDefaultChecked );
        }

        i = 0;
        while ( (elem = nodes[ i++ ]) ) {

            // #4087 - If origin and destination elements are the same, and this is
            // that element, do not do anything
            if ( selection && jQuery.inArray( elem, selection ) !== -1 ) {
                continue;
            }

            contains = jQuery.contains( elem.ownerDocument, elem );

            // Append to fragment
            tmp = getAll( safe.appendChild( elem ), "script" );

            // Preserve script evaluation history
            if ( contains ) {
                setGlobalEval( tmp );
            }

            // Capture executables
            if ( scripts ) {
                j = 0;
                while ( (elem = tmp[ j++ ]) ) {
                    if ( rscriptType.test( elem.type || "" ) ) {
                        scripts.push( elem );
                    }
                }
            }
        }

        tmp = null;

        return safe;
    };

    return $;
});

// duplicate from media/com_easydiscuss/scripts/vendors/edjquery.js
var support = {};
(function() {
    // Minified: var b,c,d,e,f,g, h,i
    var div, style, a, pixelPositionVal, boxSizingReliableVal,
        reliableHiddenOffsetsVal, reliableMarginRightVal;

    // Setup
    div = document.createElement( "div" );
    div.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>";
    a = div.getElementsByTagName( "a" )[ 0 ];
    style = a && a.style;

    // Finish early in limited (non-browser) environments
    if ( !style ) {
        return;
    }

    style.cssText = "float:left;opacity:.5";

    // Support: IE<9
    // Make sure that element opacity exists (as opposed to filter)
    support.opacity = style.opacity === "0.5";

    // Verify style float existence
    // (IE uses styleFloat instead of cssFloat)
    support.cssFloat = !!style.cssFloat;

    div.style.backgroundClip = "content-box";
    div.cloneNode( true ).style.backgroundClip = "";
    support.clearCloneStyle = div.style.backgroundClip === "content-box";

    // Support: Firefox<29, Android 2.3
    // Vendor-prefix box-sizing
    support.boxSizing = style.boxSizing === "" || style.MozBoxSizing === "" ||
        style.WebkitBoxSizing === "";

    jQuery.extend(support, {
        reliableHiddenOffsets: function() {
            if ( reliableHiddenOffsetsVal == null ) {
                computeStyleTests();
            }
            return reliableHiddenOffsetsVal;
        },

        boxSizingReliable: function() {
            if ( boxSizingReliableVal == null ) {
                computeStyleTests();
            }
            return boxSizingReliableVal;
        },

        pixelPosition: function() {
            if ( pixelPositionVal == null ) {
                computeStyleTests();
            }
            return pixelPositionVal;
        },

        // Support: Android 2.3
        reliableMarginRight: function() {
            if ( reliableMarginRightVal == null ) {
                computeStyleTests();
            }
            return reliableMarginRightVal;
        }
    });

    function computeStyleTests() {
        // Minified: var b,c,d,j
        var div, body, container, contents;

        body = document.getElementsByTagName( "body" )[ 0 ];
        if ( !body || !body.style ) {
            // Test fired too early or in an unsupported environment, exit.
            return;
        }

        // Setup
        div = document.createElement( "div" );
        container = document.createElement( "div" );
        container.style.cssText = "position:absolute;border:0;width:0;height:0;top:0;left:-9999px";
        body.appendChild( container ).appendChild( div );

        div.style.cssText =
            // Support: Firefox<29, Android 2.3
            // Vendor-prefix box-sizing
            "-webkit-box-sizing:border-box;-moz-box-sizing:border-box;" +
            "box-sizing:border-box;display:block;margin-top:1%;top:1%;" +
            "border:1px;padding:1px;width:4px;position:absolute";

        // Support: IE<9
        // Assume reasonable values in the absence of getComputedStyle
        pixelPositionVal = boxSizingReliableVal = false;
        reliableMarginRightVal = true;

        // Check for getComputedStyle so that this code is not run in IE<9.
        if ( window.getComputedStyle ) {
            pixelPositionVal = ( window.getComputedStyle( div, null ) || {} ).top !== "1%";
            boxSizingReliableVal =
                ( window.getComputedStyle( div, null ) || { width: "4px" } ).width === "4px";

            // Support: Android 2.3
            // Div with explicit width and no margin-right incorrectly
            // gets computed margin-right based on width of container (#3333)
            // WebKit Bug 13343 - getComputedStyle returns wrong value for margin-right
            contents = div.appendChild( document.createElement( "div" ) );

            // Reset CSS: box-sizing; display; margin; border; padding
            contents.style.cssText = div.style.cssText =
                // Support: Firefox<29, Android 2.3
                // Vendor-prefix box-sizing
                "-webkit-box-sizing:content-box;-moz-box-sizing:content-box;" +
                "box-sizing:content-box;display:block;margin:0;border:0;padding:0";
            contents.style.marginRight = contents.style.width = "0";
            div.style.width = "1px";

            reliableMarginRightVal =
                !parseFloat( ( window.getComputedStyle( contents, null ) || {} ).marginRight );

            div.removeChild( contents );
        }

        // Support: IE8
        // Check if table cells still have offsetWidth/Height when they are set
        // to display:none and there are still other visible table cells in a
        // table row; if so, offsetWidth/Height are not reliable for use when
        // determining if an element has been hidden directly using
        // display:none (it is still safe to use offsets if a parent element is
        // hidden; don safety goggles and see bug #4512 for more information).
        div.innerHTML = "<table><tr><td></td><td>t</td></tr></table>";
        contents = div.getElementsByTagName( "td" );
        contents[ 0 ].style.cssText = "margin:0;border:0;padding:0;display:none";
        reliableHiddenOffsetsVal = contents[ 0 ].offsetHeight === 0;
        if ( reliableHiddenOffsetsVal ) {
            contents[ 0 ].style.display = "";
            contents[ 1 ].style.display = "none";
            reliableHiddenOffsetsVal = contents[ 0 ].offsetHeight === 0;
        }

        body.removeChild( container );
    }

})();

// duplicate variables from media/com_easydiscuss/scripts/vendors/edjquery.js
var strundefined = typeof undefined;
var rcheckableType = (/^(?:checkbox|radio)$/i);
var nodeNames = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video";
var rinlinejQuery = / jQuery\d+="(?:null|\d+)"/g;
var rnoshimcache = new RegExp("<(?:" + nodeNames + ")[\\s/>]", "i");
var rleadingWhitespace = /^\s+/;
var rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi;
var rtagName = /<([\w:]+)/;
var rtbody = /<tbody/i;
var rhtml = /<|&#?\w+;/;
var rnoInnerhtml = /<(?:script|style|link)/i;
var rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i;
var rscriptType = /^$|\/(?:java|ecma)script/i;
var rscriptTypeMasked = /^true\/(.*)/;
var rcleanScript = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g;
// We have to close these tags to support XHTML (#13200)
var wrapMap = {
        option: [ 1, "<select multiple='multiple'>", "</select>" ],
        legend: [ 1, "<fieldset>", "</fieldset>" ],
        area: [ 1, "<map>", "</map>" ],
        param: [ 1, "<object>", "</object>" ],
        thead: [ 1, "<table>", "</table>" ],
        tr: [ 2, "<table><tbody>", "</tbody></table>" ],
        col: [ 2, "<table><tbody></tbody><colgroup>", "</colgroup></table>" ],
        td: [ 3, "<table><tbody><tr>", "</tr></tbody></table>" ],

        // IE6-8 can't serialize link, script, style, or any html5 (NoScope) tags,
        // unless wrapped in a div with non-breaking characters in front of it.
        _default: [ 0, "", "" ]
    };
var safeFragment = createSafeFragment( document );
var fragmentDiv = safeFragment.appendChild( document.createElement("div") );

wrapMap.optgroup = wrapMap.option;
wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
wrapMap.th = wrapMap.td;


// duplicate createSafeFragment from media/com_easydiscuss/scripts/vendors/edjquery.js
function createSafeFragment( document ) {
    var list = nodeNames.split( "|" ),
        safeFrag = document.createDocumentFragment();

    if ( safeFrag.createElement ) {
        while ( list.length ) {
            safeFrag.createElement(
                list.pop()
            );
        }
    }
    return safeFrag;
};

// Used in buildFragment, fixes the defaultChecked property
function fixDefaultChecked( elem ) {
    if ( rcheckableType.test( elem.type ) ) {
        elem.defaultChecked = elem.checked;
    }
}

// duplicate getAll from media/com_easydiscuss/scripts/vendors/edjquery.js
function getAll( context, tag ) {
    var elems, elem,
        i = 0,
        found = typeof context.getElementsByTagName !== strundefined ? context.getElementsByTagName( tag || "*" ) :
            typeof context.querySelectorAll !== strundefined ? context.querySelectorAll( tag || "*" ) :
            undefined;

    if ( !found ) {
        for ( found = [], elems = context.childNodes || context; (elem = elems[i]) != null; i++ ) {
            if ( !tag || jQuery.nodeName( elem, tag ) ) {
                found.push( elem );
            } else {
                jQuery.merge( found, getAll( elem, tag ) );
            }
        }
    }

    return tag === undefined || tag && jQuery.nodeName( context, tag ) ?
        jQuery.merge( [ context ], found ) :
        found;
};

// duplicate setGlobalEval from media/com_easydiscuss/scripts/vendors/edjquery.js
function setGlobalEval( elems, refElements ) {
    var elem,
        i = 0;
    for ( ; (elem = elems[i]) != null; i++ ) {
        jQuery._data( elem, "globalEval", !refElements || jQuery._data( refElements[i], "globalEval" ) );
    }
};
