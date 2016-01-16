/************************************************************************************************************
DG.EffectElement
Copyright (C) 2009  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2009
Owner of DHTMLgoodies.com

************************************************************************************************************/
if(!window.DG){
	window.DG = {};
}


DG.EffectElement = new Class({
	Extends : Events,
	shim : null,
	position : false,
	size : false,
	active : false,				/* In move mode */
	boundaryEl : null,
	boundaryElProps : {
		x : 0,
		y : 0,
		width : 0,
		height:0
	},
	frameSize: {
		width : 0,
		height : 0
	},
	initialize : function(el, config) {
		this.el = $(el);
		if(config.shim) {
			this.shim = config.shim;
			this._createShim();
		}
		this._setElementCssPositionProperty();
	},
	_setElementCssPositionProperty : function() {
		var position = this.el.getStyle('position');
		if(position === 'static' || position === 'fixed') {
			this.el.setStyle('position', 'absolute');
		}
	},
	_initEffect : function() {
		if(this.shim) {
			this._showShim();
		}
		this.el.setStyle('z-index', ++DG.EffectElement.zIndex);
	},
	_createShim : function() {
		if(this.shim) {
			this.shim = new Element('div');
			this.shim.addClass('dg-shim');
			var elSize = this.el.getSize();

			this.shim.setStyles({
				left : this.el.offsetLeft,
				top : this.el.offsetTop,
				width : elSize.x + 'px',
				height: elSize.y + 'px',
				position : 'absolute',
				display : 'none'
			});
			this.el.getParent().adopt(this.shim);
		}
	},
	_showShim : function() {
		this.shim.setStyles({
			display : 'block',
				left : this.el.offsetLeft,
				top : this.el.offsetTop
		});

		this.el.setStyle('display', 'none');
	},
	_hideShim : function() {
		var size = this.shim.getSize();
		var pos = this.shim.getPosition(true);
		this.shim.setStyle('display', 'none');

		this.el.setStyles({
			left : this.shim.style.left,
			top : this.shim.style.top,
			width : (size.x - this.frameSize.width) + 'px',
			height : (size.y - this.frameSize.height) + 'px',
			display : 'block'
		});
	},
	initEvents : function() {
		this.addEvents(this.listeners);
	},
	getPosition : function() {
		return this.position;
	},
	getSize : function() {
		return this.size;
	},
	_setPosition : function(x, y) {
		this.position = {
			x : x,
			y : y
		}
	},
	_setSize : function(x,y) {
		this.size = {
			x : x,
			y : y
		}
	},
	/** Get element to move or resize */
	_getEffectElement : function() {
		return this.shim ? this.shim : this.el;
	},
	_setConfigProperties : function(config) {

		if(config.boundaryEl) {
			this.boundaryEl = $(config.boundaryEl);
			if(this.boundaryEl.getStyle('position') == 'static' || this.boundaryEl.getStyle('position') == 'fixed'){
				this.boundaryEl.setStyle('position', 'relative');
			}
			this.boundaryElProps = this.boundaryEl.getCoordinates();
		}

		if(config.cls) {
			this.cls = this.cls + ' ' + config.cls;
		}

		this.listeners = config.listeners || {};

		var size = this.el.getSize();
		var pos = this.el.getPosition();
		this._setSize(size.x, size.y);
		this._setPosition(pos.x, pos.y);

		this.frameSize = {
			width : this._getFrameWidth(this.el),
			height : this._getFrameHeight(this.el)
		};
	},

	_getFrameWidth : function(el) {
		return (el.getStyle('padding-left').replace('px','')/1 +
				el.getStyle('padding-right').replace('px','')/1 +
				el.getStyle('border-left-width').replace('px','')/1 +
				el.getStyle('border-right-width').replace('px','')/1);

	},

	_getFrameHeight : function(el) {
		return (el.getStyle('padding-top').replace('px','')/1 +
				el.getStyle('padding-bottom').replace('px','')/1 +
				el.getStyle('border-top-width').replace('px','')/1 +
				el.getStyle('border-bottom-width').replace('px','')/1);

	}


});

DG.EffectElement.zIndex = 1000;