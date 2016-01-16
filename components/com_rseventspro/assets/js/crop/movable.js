/************************************************************************************************************
DG.Movable
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


DG.Movable = new Class({
	Extends: DG.EffectElement,

	handleHeight : 10,			/* height of handle */
	cls : 'dg-movable',			/* Css class for the handle */
	el : null,					/* Reference to the dragable element */
	handle: null,				/* Reference to the handle */
	shiftValue : 10,
	keyNavEnabled : true,

	initialPosition : {},

	initialize : function(el, config) {
		this.parent(el, config);
		this._setConfigProperties(config);
		this._createHandle();
		this.initEvents();

	},

	initEvents : function() {
		this.parent();
		Window.getDocument().addEvent('keydown', this._keyNavigation.bind(this));
	},

	_keyNavigation : function(e) {
		if(!this.keyNavEnabled) {
			return false;
		}

		var x = this.el.offsetLeft;
		var y = this.el.offsetTop;

		var valueChange = e.shift ? this.shiftValue : 1;

		switch(e.key) {
			case 'up' :
				y-= valueChange;
				break;
			case 'down':
				y+= valueChange;
				break;
			case 'left':
				x-= valueChange;
				break;
			case 'right':
				x+= valueChange;
				break;
			default:
				return;
		}
		this.fireEvent('beforemove', this, e);

		this._setInitialPosition(e);
		var validatedValues = this._getValidatedXY(x,y);
		this._setPosition(validatedValues.x, validatedValues.y);

		this._positionElement(this.el, validatedValues.x, validatedValues.y);
		this._positionElement(this._getEffectElement(), validatedValues.x, validatedValues.y);

		this.fireEvent('move', this, e);
		this.fireEvent('aftermove', this, e);
		return false;

	},

	_positionElement : function(el, x, y) {
		el.setStyles({
			left : x,
			top : y
		});
	},
	_setConfigProperties : function(config) {
		this.handleHeight = config.handleHeight || this.handleHeight;
		if($defined(config.keyNavEnabled)){
			this.keyNavEnabled = config.keyNavEnabled;
		}
		this.parent(config);
	},

	_initEffect : function(e) {
		if (e.target == this.handle) {
			this.fireEvent('beforemove', this);
			this.parent(e);
			this._setInitialPosition(e);
			this.active = true;
		}
		return false;
	},

	_setInitialPosition : function(e) {
		var el = this._getEffectElement();
		var size = el.getSize();
		this.initialPosition = {
			el: {
				x : el.offsetLeft,
				y : el.offsetTop,
				width : size.x,
				height : size.y
			}
		}
		if(e.page) {
			this.initialPosition.page = {
				x: e.page.x,
				y: e.page.y
			};
		}
	},
	_endMove : function(e) {
		if (this.active) {
			if (this.shim) {
				this._hideShim();
			}
			this.active = false;
			this.fireEvent('aftermove', this);
		}
	},
	_getValidatedXY : function(x,y) {
		if(this.boundaryEl) {
			if(x < 0){
				x = 0;
			}
			if(y < 0) {
				y = 0;
			}
			if(x + this.initialPosition.el.width > this.boundaryElProps.width) {
				x = this.boundaryElProps.width - this.initialPosition.el.width;
			}
			if(y + this.initialPosition.el.height > this.boundaryElProps.height) {
				y = this.boundaryElProps.height - this.initialPosition.el.height;
			}
		}
		return {
			x : x,
			y : y
		};
	},
	_move : function(e) {
		if(this.active) {
			var x = (this.initialPosition.el.x - this.initialPosition.page.x + e.page.x);
			var y = (this.initialPosition.el.y - this.initialPosition.page.y + e.page.y);
			var validatedValues = this._getValidatedXY(x,y);
			this._setPosition(validatedValues.x, validatedValues.y);
			this._positionElement(this._getEffectElement(), validatedValues.x, validatedValues.y);
			this.fireEvent('move', this, e);
		}
	},

	_createHandle : function() {
		this.handle = new Element('div');
		this.handle.addClass(this.cls);
		this.handle.set('html','<span></span>');
		this.handle.setStyles( {
			position: 'absolute',
			height: this.handleHeight,
			cursor : 'move',
			width : '100%',
			left: '0px',
			top : '0px'
		});
		this.handle.addEvent('mousedown', this._initEffect.bind(this));

		$(document.documentElement).addEvent('mouseup', this._endMove.bind(this));
		$(document.documentElement).addEvent('mousemove', this._move.bind(this));
		this.el.adopt(this.handle);

	}
});