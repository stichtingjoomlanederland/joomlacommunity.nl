/* 
 * Copyright (C) 2014 KAINOTOMO PH LTD <info@kainotomo.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

//global variable of completed function
if (typeof (SPCYEND_completed) === 'undefined') {
    var SPCYEND_completed = '';
}

cyend_log_init = function() {
    
    var message_div = document.getElementById('cyend_log');
    
    if ( (typeof(message_div) !== "undefined") && (message_div !== null) ) {
        message_div.parentNode.removeChild(message_div);
    }
    
    message_div = document.createElement("div");
    message_div.id = 'cyend_log';
    message_div.class = 'alert';
    message_div.className = 'alert alert-info';

    var message = cyend_processing_string();
    
    message_div.innerHTML = message;
    
    //document.body.insertBefore(message_div, document.body.firstChild);
    var content_div = document.getElementById('cyend-alert');
    content_div.insertBefore(message_div, content_div.firstChild);
    
};

cyend_log_message = function(message, processing) {
    
    var message_div = document.getElementById('cyend_log');
    
    message = '<p>' + message + '</p>';
    if (processing === true) {
        message = cyend_processing_string() + message;
    }
        
    message_div.innerHTML = message;
    
    //document.body.insertBefore(message_div, document.body.firstChild);
    var content_div = document.getElementById('cyend-alert');
    content_div.insertBefore(message_div, content_div.firstChild);    
};

cyend_log_remove = function() {
    
    var message_div = document.getElementById('cyend_log');
    
    if ( (typeof(message_div) !== "undefined") && (message_div !== null) ) {
        message_div.parentNode.removeChild(message_div);
    }
    
};

cyend_processing_string = function() {
    var result = Math.floor((Math.random() * 2) + 1);
    if (result === 1) {
        var m_string = 'please wait...';
    } else {
        var m_string = 'processing...';
    }
    
    return '<p><img src="../media/spcyend/images/processing.gif" alt="">'
            + ' ' + m_string + '</p>';
};

if (!XMLHttpRequest.prototype.sendAsBinary) {
    XMLHttpRequest.prototype.sendAsBinary = function(sData) {
        var nBytes = sData.length, ui8Data = new Uint8Array(nBytes);
        for (var nIdx = 0; nIdx < nBytes; nIdx++) {
            ui8Data[nIdx] = sData.charCodeAt(nIdx) & 0xff;
        }
        /* send as ArrayBufferView...: */
        this.send(ui8Data);
        /* ...or as ArrayBuffer (legacy)...: this.send(ui8Data.buffer); */
    };
}


var SPCYEND_submit = (function() {

    function ajaxSuccess() {
        /* console.log("SPCYEND_submit - Success!"); */
        //alert(this.responseText);
        window[SPCYEND_completed](this.responseText);
        /* you can get the serialized data through the "submittedData" custom property: */
        /* alert(JSON.stringify(this.submittedData)); */
    }

    function submitData(oData) {
        /* the AJAX request... */
        var oAjaxReq = new XMLHttpRequest();
        oAjaxReq.submittedData = oData;
        oAjaxReq.onload = ajaxSuccess;
        if (oData.technique === 0) {
            /* method is GET */
            oAjaxReq.open("get", oData.receiver.replace(/(?:\?.*)?$/, oData.segments.length > 0 ? "?" + oData.segments.join("&") : ""), true);
            oAjaxReq.send(null);
        } else {
            /* method is POST */
            oAjaxReq.open("post", oData.receiver, true);
            if (oData.technique === 3) {
                /* enctype is multipart/form-data */
                var sBoundary = "---------------------------" + Date.now().toString(16);
                oAjaxReq.setRequestHeader("Content-Type", "multipart\/form-data; boundary=" + sBoundary);
                oAjaxReq.sendAsBinary("--" + sBoundary + "\r\n" + oData.segments.join("--" + sBoundary + "\r\n") + "--" + sBoundary + "--\r\n");
            } else {
                /* enctype is application/x-www-form-urlencoded or text/plain */
                oAjaxReq.setRequestHeader("Content-Type", oData.contentType);
                oAjaxReq.send(oData.segments.join(oData.technique === 2 ? "\r\n" : "&"));
            }
        }
    }

    function processStatus(oData) {
        if (oData.status > 0) {
            return;
        }
        /* the form is now totally serialized! do something before sending it to the server... */
        /* doSomething(oData); */
        /* console.log("SPCYEND_submit - The form is now serialized. Submitting..."); */
        submitData(oData);
    }

    function pushSegment(oFREvt) {
        this.owner.segments[this.segmentIdx] += oFREvt.target.result + "\r\n";
        this.owner.status--;
        processStatus(this.owner);
    }

    function plainEscape(sText) {
        /* how should I treat a text/plain form encoding? what characters are not allowed? this is what I suppose...: */
        /* "4\3\7 - Einstein said E=mc2" ----> "4\\3\\7\ -\ Einstein\ said\ E\=mc2" */
        return sText.replace(/[\s\=\\]/g, "\\$&");
    }

    function SubmitRequest(oTarget) {
        var nFile, sFieldType, oField, oSegmReq, oFile, bIsPost = oTarget.method.toLowerCase() === "post";
        /* console.log("SPCYEND_submit - Serializing form..."); */
        this.contentType = bIsPost && oTarget.enctype ? oTarget.enctype : "application\/x-www-form-urlencoded";
        this.technique = bIsPost ? this.contentType === "multipart\/form-data" ? 3 : this.contentType === "text\/plain" ? 2 : 1 : 0;
        this.receiver = oTarget.action;
        this.status = 0;
        this.segments = [];
        var fFilter = this.technique === 2 ? plainEscape : escape;
        for (var nItem = 0; nItem < oTarget.elements.length; nItem++) {
            oField = oTarget.elements[nItem];
            if (!oField.hasAttribute("name")) {
                continue;
            }
            sFieldType = oField.nodeName.toUpperCase() === "INPUT" ? oField.getAttribute("type").toUpperCase() : "TEXT";
            if (sFieldType === "FILE" && oField.files.length > 0) {
                if (this.technique === 3) {
                    /* enctype is multipart/form-data */
                    for (nFile = 0; nFile < oField.files.length; nFile++) {
                        oFile = oField.files[nFile];
                        oSegmReq = new FileReader();
                        /* (custom properties:) */
                        oSegmReq.segmentIdx = this.segments.length;
                        oSegmReq.owner = this;
                        /* (end of custom properties) */
                        oSegmReq.onload = pushSegment;
                        this.segments.push("Content-Disposition: form-data; name=\"" + oField.name + "\"; filename=\"" + oFile.name + "\"\r\nContent-Type: " + oFile.type + "\r\n\r\n");
                        this.status++;
                        oSegmReq.readAsBinaryString(oFile);
                    }
                } else {
                    /* enctype is application/x-www-form-urlencoded or text/plain or method is GET: files will not be sent! */
                    for (nFile = 0; nFile < oField.files.length; this.segments.push(fFilter(oField.name) + "=" + fFilter(oField.files[nFile++].name)))
                        ;
                }
            } else if ((sFieldType !== "RADIO" && sFieldType !== "CHECKBOX") || oField.checked) {
                /* field type is not FILE or is FILE but is empty */
                this.segments.push(
                        this.technique === 3 ? /* enctype is multipart/form-data */
                        "Content-Disposition: form-data; name=\"" + oField.name + "\"\r\n\r\n" + oField.value + "\r\n"
                        : /* enctype is application/x-www-form-urlencoded or text/plain or method is GET */
                        fFilter(oField.name) + "=" + fFilter(oField.value)
                        );
            }
        }
        processStatus(this);
    }

    return function(oFormElement) {
        if (!oFormElement.action) {
            return;
        }
        new SubmitRequest(oFormElement);
    };

})();


