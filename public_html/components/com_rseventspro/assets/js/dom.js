/*
---
script: String.toDOM.js
description: Convert a string into DOM nodes
license: MIT-style license.
authors: Yannick Croissant
requires:
- core
provides: [String.toDOM]
...
*/
String.implement({
	
	toDOM: function(){
		var wrapper =	this.test('^<the|^<tf|^<tb|^<colg|^<ca') && ['<table>', '</table>', 1] ||
						this.test('^<col') && ['<table><colgroup>', '</colgroup><tbody></tbody></table>',2] ||
						this.test('^<tr') && ['<table><tbody>', '</tbody></table>', 2] ||
						this.test('^<th|^<td') && ['<table><tbody><tr>', '</tr></tbody></table>', 3] ||
						this.test('^<li') && ['<ul>', '</ul>', 1] ||
						this.test('^<dt|^<dd') && ['<dl>', '</dl>', 1] ||
						this.test('^<le') && ['<fieldset>', '</fieldset>', 1] ||
						this.test('^<opt') && ['<select multiple="multiple">', '</select>', 1] ||
						['', '', 0];
		if (Browser.ie8 || Browser.ie7) {
			if (wrapper[0].test('table')) {
				var el = new Element('div', {html: wrapper[0] + this + wrapper[1]}).getChildren();
				while(wrapper[2]--) el = el[0].getChildren();
			} else {
				var el = new Element('div', {html: wrapper[0] + this + wrapper[1]}).getElements('li');
				while(wrapper[2]--) el = el[0].getElements('li');
			}
		} else {
			var el = new Element('div', {html: wrapper[0] + this + wrapper[1]}).getChildren();
			while(wrapper[2]--) el = el[0].getChildren();
		}
		
		return el;
	}
});