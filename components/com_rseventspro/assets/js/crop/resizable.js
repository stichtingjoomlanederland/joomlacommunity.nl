/************************************************************************************************************
DG.Resizable
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


DG.Resizable = new Class({
	Extends : DG.EffectElement,

	preserveAspectRatio: false,
	handleSize : 10,
	handleOffset : 0,
	minWidth : 20,
	minHeight : 20,
	maxWidth : 10000,
	maxHeight : 10000,
	handles : 'se',

	cls : 'dg-resize-handle',
	resizeProperties : {
		direction : '',
		preserveAspectRatio : false,
		el : {},
		event : {}
	},

	initialize : function(el, config) {
		this.parent(el, config);
		this._setConfigProperties(config);
		this._createHandles();
	},
	_setConfigProperties : function(config) {
		var keys = ['handleSize', 'handles', 'handleOffset', 'preserveAspectRatio', 'minWidth','minHeight','maxWidth','maxHeight'];

		for(var i=0;i<keys.length;i++) {
			var key = keys[i];
			this[key] = config[key] || this[key];
		}
		this.parent(config);
	},
	_getHandleStyles : function(direction) {
		var ret = {
			'left' : 0,
			'top' : 0,
			'margin-left' : 0,
			'margin-top' : 0,
			width : this.handleSize + 'px',
			height : this.handleSize + 'px',
			position : 'absolute',
			cursor : direction + '-resize'
		}

		var marginOffset = (this.handleSize * -1) + this.handleOffset;

		var centerMarginOffset = Math.round(this.handleSize / 2) * -1;
		var cornerMarginOffset = this.handleSize *-1;

		if(direction == 'n' || direction=='s') {
			ret.left = '50%';
			ret['margin-left'] = centerMarginOffset;
		}
		if(direction == 'w' || direction == 'e') {
			ret.top = '50%';
			ret['margin-top'] = centerMarginOffset;
		}

		if(direction == 'se' || direction=='e' || direction=='ne') {
			ret.left = '100%';
			ret['margin-left'] = cornerMarginOffset;
		}

		if(direction == 'se' || direction == 's' || direction == 'sw') {
			ret.top = '100%';
			ret['margin-top'] = cornerMarginOffset;
		}

		if(this.handleOffset) {
			ret['margin-top'] += ret['margin-top'] ? this.handleOffset : 0;
			ret['margin-left'] += ret['margin-left'] ? this.handleOffset : 0;
			if(!ret.left) {
				ret.left -= this.handleOffset;
			}
			if(!ret.top) {
				ret.top -= this.handleOffset;
			}
		}
		return ret;
	},
	_setResizeCoordinates : function(e, x,y, width, height) {
		this.resizeProperties.el ={
			x : x,
			y : y,
			width : width,
			height : height
		};
		this.resizeProperties.event = {
			x : e.page.x,
			y : e.page.y
		}
	},

	_initEffect : function(e) {
		var direction;
		if(direction = e.target.getProperty('direction')) {
			this.fireEvent('beforeresize', this);
			this.parent(e);
			var pos = {
				x: this._getEffectElement().offsetLeft,
				y: this._getEffectElement().offsetTop
			};
			var size = this._getEffectElement().getSize();

			size.x -= this._getFrameWidth(this._getEffectElement());
			size.y -= this._getFrameHeight(this._getEffectElement());

			this.resizeProperties = {
				direction : direction,
				preserveAspectRatio : this.preserveAspectRatio
			}
			this._setResizeCoordinates(e, pos.x, pos.y, size.x, size.y);
			this.active = true;
		}
		return false;
	},
	_resize : function(e) {
		if(this.active) {
			var preserveRatio = this.resizeProperties.preserveAspectRatio || e.shift;
			if(preserveRatio && !this.resizeProperties.ratio) {
				this.resizeProperties.ratio = this._getEffectElement().offsetWidth / this._getEffectElement().offsetHeight;
			}else{
				if (!preserveRatio) {
					this.resizeProperties.ratio = false;
				}
			}
			var coordinates = this._getNewCoordinates(e, preserveRatio);
			this._getEffectElement().setStyles(coordinates);
			this._setSize(coordinates.width, coordinates.height);
			this._setPosition(coordinates.left, coordinates.top);
			this.fireEvent('resize', this);
		}
	},
	_getMinWidth : function() {
		return this.minWidth;
	},
	_getMinHeight : function() {
		return this.minHeight;
	},
	_getMaxWidth : function(dir) {
		if (this.boundaryEl) {
			return Math.min(this.maxWidth, this.boundaryElProps.width - this.resizeProperties.el.x - this.frameSize.width);
		}
		else {
			return this.maxWidth;
		}
	},
	_getMaxHeight : function(dir) {
		if (this.boundaryEl) {
			return Math.min(this.maxHeight, this.boundaryElProps.height - this.resizeProperties.el.y - this.frameSize.height);
		}else{
			return this.maxHeight;
		}
	},
	_setHeight : function(ret, heightChange, preserveRatio, adjustLeft) {
		ret.height = this.resizeProperties.el.height + heightChange;
		return ret;
	},
	_setWidth : function(ret, widthChange, preserveRatio, adjustTop) {
		ret.width = this.resizeProperties.el.width + widthChange;
		return ret;

	},
	_setLeft : function(ret, widthChange) {
		ret.left = this.resizeProperties.el.x + widthChange;
		return ret;
	},
	_setTop : function(ret, heightChange) {
		ret.top = this.resizeProperties.el.y + heightChange;
		return ret;
	},
	_getValidatedWidthChange : function(widthChange,dir ) {
		var minWidth = this._getMinWidth();
		var maxWidth = this._getMaxWidth(dir);
		if (dir == 'e' || dir == 'se' || dir == 'nw' || dir=='s' || dir=='n' || dir=='ne') {
			if ((widthChange + this.resizeProperties.el.width) > maxWidth) {
				widthChange -= (widthChange + this.resizeProperties.el.width) - maxWidth;
			}
		}
		if ((widthChange + this.resizeProperties.el.width) < minWidth) {
			widthChange -= (widthChange + this.resizeProperties.el.width) - minWidth;
		}
		return widthChange;
	},
	_getValidatedHeightChange : function(heightChange, dir) {
		var minHeight = this._getMinHeight();
		var maxHeight = this._getMaxHeight(dir);
		if (dir == 's' || dir == 'se' || dir == 'sw' || dir=='e' || dir=='w') {
			if ((heightChange + this.resizeProperties.el.height) > maxHeight) {
				heightChange -= (heightChange + this.resizeProperties.el.height) - maxHeight;
			}
		}
		if ((heightChange + this.resizeProperties.el.height) < minHeight) {
			heightChange -= (heightChange + this.resizeProperties.el.height) - minHeight;
		}
		return heightChange;

	},
	/* return new coordinates for the resize */
	_getNewCoordinates: function(e, preserveRatio){
		var ret = {};
		var dir = this.resizeProperties.direction;
		var ratio = this.resizeProperties.ratio;

		var widthChange = e.page.x - this.resizeProperties.event.x;
		var heightChange = e.page.y - this.resizeProperties.event.y;

		switch (dir) {
			case 'nw':
				widthChange *= -1;
				heightChange *= -1;
				break;
			case 'w':
			case 'sw':
				widthChange *= -1;
				break;
			case 'n':
			case 'ne':
				heightChange *= -1;
				break;
		}

		if (preserveRatio) {
			switch (dir) {
				case 'n':
				case 'nw':
				case 's':
					heightChange = this._getValidatedHeightChange(heightChange, dir);
					widthChange = heightChange * ratio;
					if (dir != 'nw') {
						widthChange = this._getValidatedWidthChange(widthChange, dir);
						heightChange = widthChange / ratio;
					}
					break;
				default:
					var tmp = widthChange;
					widthChange = this._getValidatedWidthChange(widthChange, dir);
					heightChange = widthChange / ratio;
					heightChange = this._getValidatedHeightChange(heightChange, dir);
					widthChange = heightChange * ratio;
			}
		}
		else {
			if (dir != 'nw') {
				widthChange = this._getValidatedWidthChange(widthChange, dir);
			}
			heightChange = this._getValidatedHeightChange(heightChange, dir);

			if (dir == 's' || dir == 'n') {
				widthChange = 0;
			}
			if (dir == 'e' || dir == 'w') {
				heightChange = 0;
			}
		}

		if (dir == 'w' || dir == 'nw' || dir == 'sw') {
			if (this.resizeProperties.el.x - widthChange < 0) {
				widthChange = this.resizeProperties.el.x;
				if (preserveRatio) {
					heightChange = widthChange / ratio;
				}
			}
		}
		if (dir == 'n' || dir == 'nw' || dir == 'ne') {
			if (this.resizeProperties.el.y - heightChange < 0) {
				heightChange = this.resizeProperties.el.y;
				if (preserveRatio) {
					widthChange = heightChange * ratio;
				}
			}
		}

		ret = this._setWidth(ret, widthChange);
		ret = this._setHeight(ret, heightChange);
		if (dir == 'nw') {
			ret = this._setLeft(ret, widthChange * -1);
			ret = this._setTop(ret, heightChange * -1);
		}
		if (dir == 'ne' || dir == 'n') {
			ret = this._setTop(ret, heightChange * -1);
		}
		if (dir == 'sw' || dir == 'w') {
			ret = this._setLeft(ret, widthChange * -1);
		}

		this._updateEffectProperties(e, ret);
		return ret;
	},
	_updateEffectProperties: function(e, coords){
		this.resizeProperties.event.x = e.page.x;
		this.resizeProperties.event.y = e.page.y;
		this.resizeProperties.el.width = coords.width;
		this.resizeProperties.el.height = coords.height;
		if (coords.top || coords.top === 0) {
			this.resizeProperties.el.y = coords.top;
		}
		if (coords.left || coords.left === 0) {
			this.resizeProperties.el.x = coords.left;
		}
	},
	_endResize : function(e) {
		if(this.active) {
			this.active = false;
			if(this.shim) {
				this._hideShim();
			}
			this.fireEvent('afterresize', this);
		}
	},
	_createHandles : function() {
		var handles = this.handles.split(/,/g);
		if(this.handles == 'all') {
			handles = ['nw','w','sw','n','ne','e','se','s'];
		}
		this.handles = {};

		for(var i=0;i<handles.length;i++) {
			var el = this.handles[handles[i]] = new Element('div');
			el.addClass(this.cls);
			el.setProperty('direction', handles[i]);
			el.setStyles(this._getHandleStyles(handles[i]));
			el.addEvent('mousedown', this._initEffect.bind(this), this);
			this.el.adopt(el);
		}

		$(document.documentElement).addEvent('mouseup', this._endResize.bind(this));
		$(document.documentElement).addEvent('mousemove', this._resize.bind(this));
	}
});

