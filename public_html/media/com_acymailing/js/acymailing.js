/**
 * @package    AcyMailing for Joomla!
 * @version    5.5.0
 * @author     acyba.com
 * @copyright  (C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

function tableOrdering(order, dir, task){
	var form = document.adminForm;

	form.filter_order.value = order;
	form.filter_order_Dir.value = dir;
	submitform(task);
}

function submitform(pressbutton){
	if(pressbutton){
		document.adminForm.task.value = pressbutton;
	}
	if(typeof document.adminForm.onsubmit == "function"){
		document.adminForm.onsubmit();
	}
	document.adminForm.submit();
}

function checkChangeForm(){
	var varform = document['adminForm'];
	nameField = varform.elements['data[subscriber][name]'];
	if(nameField && (( typeof acymailing != 'undefined' && nameField.value == acymailing['NAMECAPTION'] ) || nameField.value.replace(/ /g, "").length < 2)){
		if(typeof acymailing != 'undefined'){
			alert(acymailing['NAME_MISSING']);
		}
		nameField.className = nameField.className + ' invalid';
		return false;
	}

	var emailField = varform.elements['data[subscriber][email]'];
	if(emailField){
		if(typeof acymailing == 'undefined' || emailField.value != acymailing['EMAILCAPTION']) emailField.value = emailField.value.replace(/ /g, "");
		var filter = /^([a-z0-9_'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+$/i;
		if(!emailField || (typeof acymailing != 'undefined' && emailField.value == acymailing['EMAILCAPTION']) || !filter.test(emailField.value)){
			if(typeof acymailing != 'undefined'){
				alert(acymailing['VALID_EMAIL']);
			}
			emailField.className = emailField.className + ' invalid';
			return false;
		}
	}

	if(typeof acymailing != 'undefined' && typeof acymailing['reqFieldsComp'] != 'undefined' && acymailing['reqFieldsComp'].length > 0){
		for(var i = 0; i < acymailing['reqFieldsComp'].length; i++){
			elementName = 'data[subscriber][' + acymailing['reqFieldsComp'][i] + ']';
			elementToCheck = varform.elements[elementName];
			if(elementToCheck){
				var isValid = false;
				if(typeof elementToCheck.value != 'undefined'){
					if(elementToCheck.value == ' ' && typeof varform[elementName + '[]'] != 'undefined'){
						if(varform[elementName + '[]'].checked){
							isValid = true;
						}else{
							for(var a = 0; a < varform[elementName + '[]'].length; a++){
								if((varform[elementName + '[]'][a].checked || varform[elementName + '[]'][a].selected) && varform[elementName + '[]'][a].value.length > 0) isValid = true;
							}
						}
					}else{
						if(elementToCheck.value.replace(/ /g, "").length > 0) isValid = true;
					}
				}else{
					for(var a = 0; a < elementToCheck.length; a++){
						if(elementToCheck[a].checked && elementToCheck[a].value.length > 0) isValid = true;
					}
				}
				if((elementToCheck.length >= 1 && (elementToCheck[0].parentElement.parentElement.style.display == 'none' || elementToCheck[0].parentElement.parentElement.parentElement.style.display == 'none')) || (typeof elementToCheck.length == 'undefined' && (elementToCheck.parentElement.parentElement.style.display == 'none' || elementToCheck.parentElement.parentElement.parentElement.style.display == 'none'))){
					isValid = true;
				}
				if(!isValid){
					elementToCheck.className = elementToCheck.className + ' invalid';
					alert(acymailing['validFieldsComp'][i]);
					return false;
				}
			}else{
				if((varform.elements[elementName + '[day]'] && varform.elements[elementName + '[day]'].value < 1) || (varform.elements[elementName + '[month]'] && varform.elements[elementName + '[month]'].value < 1) || (varform.elements[elementName + '[year]'] && varform.elements[elementName + '[year]'].value < 1902)){
					if(varform.elements[elementName + '[day]'] && varform.elements[elementName + '[day]'].value < 1) varform.elements[elementName + '[day]'].className = varform.elements[elementName + '[day]'].className + ' invalid';
					if(varform.elements[elementName + '[month]'] && varform.elements[elementName + '[month]'].value < 1) varform.elements[elementName + '[month]'].className = varform.elements[elementName + '[month]'].className + ' invalid';
					if(varform.elements[elementName + '[year]'] && varform.elements[elementName + '[year]'].value < 1902) varform.elements[elementName + '[year]'].className = varform.elements[elementName + '[year]'].className + ' invalid';
					alert(acymailing['validFieldsComp'][i]);
					return false;
				}

				if((varform.elements[elementName + '[country]'] && varform.elements[elementName + '[country]'].value < 1) || (varform.elements[elementName + '[num]'] && varform.elements[elementName + '[num]'].value < 3)){
					if(varform.elements[elementName + '[country]'] && varform.elements[elementName + '[country]'].value < 1) varform.elements[elementName + '[country]'].className = varform.elements[elementName + '[country]'].className + ' invalid';
					if(varform.elements[elementName + '[num]'] && varform.elements[elementName + '[num]'].value < 3) varform.elements[elementName + '[num]'].className = varform.elements[elementName + '[num]'].className + ' invalid';
					alert(acymailing['validFieldsComp'][i]);
					return false;
				}
			}
		}
	}

	if(typeof acymailing != 'undefined' && typeof acymailing['checkFields'] != 'undefined' && acymailing['checkFields'].length > 0){
		for(var i = 0; i < acymailing['checkFields'].length; i++){
			elementName = 'data[subscriber][' + acymailing['checkFields'][i] + ']';
			elementtypeToCheck = acymailing['checkFieldsType'][i];
			elementToCheck = varform.elements[elementName].value;
			switch(elementtypeToCheck){
				case 'number':
					myregexp = new RegExp('^[0-9]*$');
					break;
				case 'letter':
					myregexp = new RegExp('^[A-Za-z\u00C0-\u017F ]*$');
					break;
				case 'letnum':
					myregexp = new RegExp('^[0-9a-zA-Z\u00C0-\u017F ]*$');
					break;
				case 'regexp':
					myregexp = new RegExp(acymailing['checkFieldsRegexp'][i]);
					break;
			}
			if(!myregexp.test(elementToCheck)){
				alert(acymailing['validCheckFields'][i]);
				return false;
			}
		}
	}

	var captchaField = varform.elements['acycaptcha'];
	if(captchaField){
		if(captchaField.value.length < 1){
			if(typeof acymailing != 'undefined'){
				alert(acymailing['CAPTCHA_MISSING']);
			}
			captchaField.className = captchaField.className + ' invalid';
			return false;
		}
	}
	return true;
}

(function(){
	function preventDefault(){
		this.returnValue = false;
	}

	function stopPropagation(){
		this.cancelBubble = true;
	}

	var Oby = {
		version: 20120930, ajaxEvents: {},

		hasClass: function(o, n){
			if(o.className == '') return false;
			var reg = new RegExp("(^|\\s+)" + n + "(\\s+|$)");
			return reg.test(o.className);
		}, addClass: function(o, n){
			if(!this.hasClass(o, n)){
				if(o.className == ''){
					o.className = n;
				}else{
					o.className += ' ' + n;
				}
			}
		}, trim: function(s){
			return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');
		}, removeClass: function(e, c){
			var t = this;
			if(t.hasClass(e, c)){
				var cn = ' ' + e.className + ' ';
				e.className = t.trim(cn.replace(' ' + c + ' ', ''));
			}
		}, addEvent: function(d, e, f){
			if(d.attachEvent){
				d.attachEvent('on' + e, f);
			}else if(d.addEventListener){
				d.addEventListener(e, f, false);
			}else{
				d['on' + e] = f;
			}
			return f;
		}, removeEvent: function(d, e, f){
			try{
				if(d.detachEvent){
					d.detachEvent('on' + e, f);
				}else if(d.removeEventListener){
					d.removeEventListener(e, f, false);
				}else{
					d['on' + e] = null;
				}
			}catch(e){
			}
		}, cancelEvent: function(e){
			if(!e){
				e = window.event;
				if(!e){
					return false;
				}
			}
			if(e.stopPropagation){
				e.stopPropagation();
			}else{
				e.cancelBubble = true;
			}
			if(e.preventDefault){
				e.preventDefault();
			}else{
				e.returnValue = false;
			}
			return false;
		}, evalJSON: function(text, secure){
			if(typeof(text) != "string" || !text.length) return null;
			if(secure && !(/^[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]*$/).test(text.replace(/\\./g, '@').replace(/"[^"\\\n\r]*"/g, ''))) return null;
			return eval('(' + text + ')');
		}, getXHR: function(){
			var xhr = null, w = window;
			if(w.XMLHttpRequest || w.ActiveXObject){
				if(w.ActiveXObject){
					try{
						xhr = new ActiveXObject("Microsoft.XMLHTTP");
					}catch(e){
					}
				}else{
					xhr = new w.XMLHttpRequest();
				}
			}
			return xhr;
		}, xRequest: function(url, options, cb, cbError){
			var t = this, xhr = t.getXHR();
			if(!options) options = {};
			if(!cb){
				cb = function(){
				};
			}
			options.mode = options.mode || 'GET';
			options.update = options.update || false;
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					if(xhr.status == 200 || (xhr.status == 0 && xhr.responseText > 0) || !cbError){
						if(cb){
							cb(xhr, options.params);
						}
						if(options.update){
							t.updateElem(options.update, xhr.responseText);
						}
					}else{
						cbError(xhr, options.params);
					}
				}
			};
			xhr.open(options.mode, url, true);
			if(options.mode.toUpperCase() == 'POST'){
				xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			}
			xhr.send(options.data);
		}, getFormData: function(target){
			var d = document, ret = '';
			if(typeof(target) == 'string'){
				target = d.getElementById(target);
			}
			if(target === undefined){
				target = d;
			}
			var typelist = ['input', 'select', 'textarea'];
			for(var t in typelist){
				t = typelist[t];
				var inputs = target.getElementsByTagName(t);
				for(var i = inputs.length - 1; i >= 0; i--){
					if(inputs[i].name && !inputs[i].disabled){
						var evalue = inputs[i].value, etype = '';
						if(t == 'input'){
							etype = inputs[i].type.toLowerCase();
						}
						if(etype == 'radio' && !inputs[i].checked){
							evalue = null;
						}
						if((etype != 'file' && etype != 'submit') && evalue != null){
							if(ret != '') ret += '&';
							ret += encodeURI(inputs[i].name) + '=' + encodeURIComponent(evalue);
						}
					}
				}
			}
			return ret;
		}, updateElem: function(elem, data){
			var d = document, scripts = '';
			if(typeof(elem) == 'string'){
				elem = d.getElementById(elem);
			}
			var text = data.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(all, code){
				scripts += code + '\n';
				return '';
			});
			elem.innerHTML = text;
			if(scripts != ''){
				var script = d.createElement('script');
				script.setAttribute('type', 'text/javascript');
				script.text = scripts;
				d.head.appendChild(script);
				d.head.removeChild(script);
			}
		}
	};

	var acymailing = {
		submitFct: null, submitBox: function(data){
			var t = this, d = document, w = window;
			if(t.submitFct){
				try{
					t.submitFct(data);
				}catch(err){
				}
			}
			t.closeBox();
		}, deleteId: function(id){
			var t = this, d = document, el = id;
			if(typeof(id) == "string"){
				el = d.getElementById(id);
			}
			if(!el){
				return;
			}
			el.parentNode.removeChild(el);
		}, dup: function(tplName, htmlblocks, id, extraData, appendTo){
			var d = document, tplElem = d.getElementById(tplName), container = tplElem.parentNode;
			if(!tplElem) return;
			elem = tplElem.cloneNode(true);
			if(!appendTo){
				container.insertBefore(elem, tplElem);
			}else{
				if(typeof(appendTo) == "string"){
					appendTo = d.getElementById(appendTo);
				}
				appendTo.appendChild(elem);
			}
			elem.style.display = "";
			elem.id = '';
			if(id){
				elem.id = id;
			}
			for(var k in htmlblocks){
				elem.innerHTML = elem.innerHTML.replace(new RegExp("{" + k + "}", "g"), htmlblocks[k]);
				elem.innerHTML = elem.innerHTML.replace(new RegExp("%7B" + k + "%7D", "g"), htmlblocks[k]);
			}
			if(extraData){
				for(var k in extraData){
					elem.innerHTML = elem.innerHTML.replace(new RegExp('{' + k + '}', 'g'), extraData[k]);
					elem.innerHTML = elem.innerHTML.replace(new RegExp('%7B' + k + '%7D', 'g'), extraData[k]);
				}
			}
		}, deleteRow: function(id){
			var t = this, d = document, el = id;
			if(typeof(id) == "string"){
				el = d.getElementById(id);
			}else{
				while(el != null && el.tagName.toLowerCase() != 'tr'){
					el = el.parentNode;
				}
			}
			if(!el){
				return;
			}
			var table = el.parentNode;
			table.removeChild(el);
			if(table.tagName.toLowerCase() == 'tbody'){
				table = table.parentNode;
			}
			t.cleanTableRows(table);
			return;
		}, dupRow: function(tplName, htmlblocks, id, extraData){
			var d = document, tplLine = d.getElementById(tplName), tableUser = tplLine.parentNode;
			if(!tplLine) return;
			trLine = tplLine.cloneNode(true);
			tableUser.appendChild(trLine);
			trLine.style.display = "";
			trLine.id = "";
			if(id){
				trLine.id = id;
			}
			for(var i = tplLine.cells.length - 1; i >= 0; i--){
				if(trLine.cells[i]){
					for(var k in htmlblocks){
						trLine.cells[i].innerHTML = trLine.cells[i].innerHTML.replace(new RegExp("{" + k + "}", "g"), htmlblocks[k]);
						trLine.cells[i].innerHTML = trLine.cells[i].innerHTML.replace(new RegExp("%7B" + k + "%7D", "g"), htmlblocks[k]);
					}
					if(extraData){
						for(var k in extraData){
							trLine.cells[i].innerHTML = trLine.cells[i].innerHTML.replace(new RegExp('{' + k + '}', 'g'), extraData[k]);
							trLine.cells[i].innerHTML = trLine.cells[i].innerHTML.replace(new RegExp('%7B' + k + '%7D', 'g'), extraData[k]);
						}
					}
				}
			}
			if(tplLine.className == "row0") tplLine.className = "row1";else if(tplLine.className == "row1") tplLine.className = "row0";
		}, cleanTableRows: function(id){
			var d = document, el = id;
			if(typeof(id) == "string"){
				el = d.getElementById(id);
			}
			if(el == null || el.tagName.toLowerCase() != 'table'){
				return;
			}

			var k = 0, c = '', line = null, lines = el.getElementsByTagName('tr');
			for(var i = 0; i < lines.length; i++){
				line = lines[i];
				if(line.style.display != "none"){
					c = ' ' + line.className + ' ';
					if(c.indexOf(' row0 ') >= 0 || c.indexOf(' row1 ') >= 0){
						line.className = c.replace(' row' + (1 - k) + ' ', ' row' + k + ' ').replace(/^\s*|\s*$/g, '');
						k = 1 - k;
					}
				}
			}
		}, checkRow: function(id){
			var t = this, d = document, el = id;
			if(typeof(id) == "string"){
				el = d.getElementById(id);
			}
			if(el == null || el.tagName.toLowerCase() != 'input'){
				return;
			}
			if(this.clicked){
				this.clicked = null;
				t.isChecked(el);
				return;
			}
			el.checked = !el.checked;
			t.isChecked(el);
		}, isChecked: function(id, cancel){
			var d = document, el = id;
			if(typeof(id) == "string"){
				el = d.getElementById(id);
			}
			if(el == null || el.tagName.toLowerCase() != 'input'){
				return;
			}
			if(el.form.boxchecked){
				if(el.checked){
					el.form.boxchecked.value++;
				}else{
					el.form.boxchecked.value--;
				}
			}
		}, checkAll: function(checkbox, stub){
			stub = stub || 'cb';
			if(checkbox.form){
				var cb = checkbox.form, c = 0;
				for(var i = 0, n = cb.elements.length; i < n; i++){
					var e = cb.elements[i];
					if(e.type == checkbox.type){
						if((stub && e.id.indexOf(stub) == 0) || !stub){
							e.checked = checkbox.checked;
							c += (e.checked == true ? 1 : 0);
						}
					}
				}
				if(cb.boxchecked){
					cb.boxchecked.value = c;
				}
				return true;
			}
			return false;
		}, submitform: function(task, form, extra){
			var d = document;
			if(typeof form == 'string'){
				var f = d.getElementById(form);
				if(!f){
					f = d.getElementByName(form);
				}
				if(!f){
					return true;
				}
				form = f;
			}
			if(task){
				form.task.value = task;
			}
			if(typeof form.onsubmit == 'function'){
				form.onsubmit();
			}
			form.submit();
			return false;
		}, get: function(elem, target){
			window.Oby.xRequest(elem.getAttribute('href'), {update: target});
			return false;
		}, form: function(elem, target){
			var data = window.Oby.getFormData(target);
			window.Oby.xRequest(elem.getAttribute('href'), {update: target, mode: 'POST', data: data});
			return false;
		}, openBox: function(elem, url, jqmodal){
			var w = window;
			if(typeof(elem) == "string"){
				elem = document.getElementById(elem);
			}
			if(!elem){
				return false;
			}
			try{
				if(jqmodal === undefined || typeof(jQuery) == "undefined"){
					jqmodal = false;
				}
				if(!jqmodal && w.SqueezeBox !== undefined){
					if(url !== undefined){
						elem.href = url;
					}
					if(w.SqueezeBox.open !== undefined){
						SqueezeBox.open(elem, {parse: 'rel'});
					}else if(w.SqueezeBox.fromElement !== undefined){
						SqueezeBox.fromElement(elem);
					}
				}else{
					var id = elem.getAttribute('id');
					this.currentBox = id;

					try{
						jQuery('#modal-' + id).modal('show');
					}catch(e){
						if(w.SqueezeBox !== undefined){
							if(url){
								elem.href = url;
							}
							if(w.SqueezeBox.open !== undefined){
								SqueezeBox.open(elem, {parse: 'rel'});
							}else if(w.SqueezeBox.fromElement !== undefined){
								SqueezeBox.fromElement(elem);
							}
						}
					}

					if(url){
						if(document.getElementById('modal-' + id + '-container')){
							jQuery('#modal-' + id + '-container').find('iframe').attr('src', url);
						}else{
							jQuery('#modal-' + id).find('iframe').attr('src', url);
						}
					}
				}
			}catch(e){
			}
			return false;
		}, closeBox: function(name){
			var d = document, w = window;
			try{
				if(d.getElementById('sbox-window')){
					d.getElementById('sbox-window').close();
				}else if(w.SqueezeBox !== undefined){
					w.SqueezeBox.close();
				}else if(name !== undefined && jQuery !== undefined){
					jQuery('#' + name).modal('hide');
				}
			}catch(err){
			}
		}, tabSelect: function(m, c, id){
			var d = document, sub = null;
			if(typeof m == 'string'){
				m = d.getElementById(m);
			}
			if(typeof id == 'string'){
				id = d.getElementById(id);
			}
			sub = m.getElementsByTagName('div');
			for(var i = sub.length - 1; i >= 0; i--){
				if(sub[i].getAttribute('class') == c){
					sub[i].style.display = 'none';
				}
			}
			id.style.display = '';
		}, getOffset: function(el){
			var x = 0, y = 0;
			while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)){
				x += el.offsetLeft - el.scrollLeft;
				y += el.offsetTop - el.scrollTop;
				el = el.offsetParent;
			}
			return {top: y, left: x};
		}
	};

	if((typeof(window.Oby) == 'undefined') || window.Oby.version < Oby.version){
		window.Oby = Oby;
		window.obscurelighty = Oby;
	}
	window.acymailing = acymailing;
})();
