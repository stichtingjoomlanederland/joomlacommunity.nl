/************************************************************************************************************
DG Image Crop
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

DG.ImageCrop = new Class({
	Extends : Events,

	movable : null,
	resizable : null,
	cropRectangle : null,
	ratio : 1,
	el: null,
	lazyShadows : true,

	resizeConfig: {
		handleSize : 7,
		handleOffset : 4
	},

	shadows : {
		west : null,
		north : null,
		east : null,
		west: null
	},

	initialize : function(el, config) {
		this.el = $(el);

		this._setConfigProperties(config);
		this._createCropRectangle();
		this._createMovable();
		this._createResizable();
		this._createShadows();
		this.initEvents();
		this._positionShadows();
		this.fireEvent('render', this);
	},

	maximize : function() {
		this.fireEvent('beforecrop', this);
		var styles = {
			left:0,
			top : 0,
			width: this.el.offsetWidth - this._getFrameWidth(this.cropRectangle),
			height : this.el.offsetHeight - this._getFrameHeight(this.cropRectangle)
		};
		this.cropRectangle.setStyles(styles);
		this._positionShadows();
		this.fireEvent('crop', this);
		this.fireEvent('aftercrop', this);

	},

	minimize : function() {
		this.fireEvent('beforecrop', this);
		var styles = {
			width: this.resizable.minWidth,
			height :  this.resizable.minHeight
		};
		this.cropRectangle.setStyles(styles);
		this._positionShadows();
		this.fireEvent('crop', this);
		this.fireEvent('aftercrop', this);
	},

	alignTo : function(pos) {
		this.fireEvent('beforecrop', this);

		var x = 0;
		var y = 0;


		if(pos=='e' || pos =='ne' || pos == 'se') {
			x = this.el.offsetWidth - this.cropRectangle.offsetWidth;
		}

		if(pos=='se' || pos =='sw' || pos == 's') {
			y = this.el.offsetHeight - this.cropRectangle.offsetHeight;
		}

		if(pos=='w' || pos=='e' || pos=='center' || pos == 'vcenter') {
			y = (this.el.offsetHeight/2 - this.cropRectangle.offsetHeight/2);
		}
		if(pos=='n' || pos=='s' || pos=='center' || pos == 'hcenter') {
			x = (this.el.offsetWidth/2 - this.cropRectangle.offsetWidth/2);
		}

		if(pos=='hcenter') {
			y = this.cropRectangle.offsetTop;
		}
		if(pos=='vcenter') {
			x = this.cropRectangle.offsetLeft;
		}

		var styles = {
			left : Math.round(x),
			top : Math.round(y)
		};

		this.cropRectangle.setStyles(styles);
		this._positionShadows();
		this.fireEvent('crop', this);
		this.fireEvent('aftercrop', this);
	},

	getCoordinates : function() {
		return{
			left : Math.round(this.cropRectangle.offsetLeft * this.ratio),
			top : Math.round(this.cropRectangle.offsetTop * this.ratio),
			width :  Math.round(this.cropRectangle.offsetWidth * this.ratio),
			height :Math.round(this.cropRectangle.offsetHeight * this.ratio)
		}

	},

	getPreviewCoordinates : function() {
		return{
			left : this.cropRectangle.offsetLeft,
			top : this.cropRectangle.offsetTop,
			width :  this.cropRectangle.offsetWidth,
			height :this.cropRectangle.offsetHeight
		}
	},

	_createShadows : function() {

		this.shadows.west = new Element('DIV');
		this.shadows.west.addClass('dg-crop-shadow');
		this.shadows.west.setStyles({
			left: 0,
			position : 'absolute'
		});
		this.el.adopt(this.shadows.west);

		this.shadows.east = new Element('DIV');
		this.shadows.east.addClass('dg-crop-shadow');
		this.shadows.east.setStyles({
			right: 0,
			position : 'absolute'
		});
		this.el.adopt(this.shadows.east);

		this.shadows.north = new Element('DIV');
		this.shadows.north.addClass('dg-crop-shadow');
		this.shadows.north.setStyles({
			top: 0,
			left : 0,
			width : '100%',
			position : 'absolute'
		});
		this.el.adopt(this.shadows.north);

		this.shadows.south = new Element('DIV');
		this.shadows.south.addClass('dg-crop-shadow');
		this.shadows.south.setStyles({
			bottom: 0,
			left : 0,
			width : '100%',
			position : 'absolute'
		});
		this.el.adopt(this.shadows.south);

		/* Ie fix because the manual styling routine in _positionShadows() works too slow in IE */
		if(Browser.Engine.name == 'trident' && !this.lazyShadows) {
			var refName = this._getUnique();
			this.shadows.west.style.cssText = 'left:0px;position:absolute;top:expression(window.' + refName + '.cropRectangle.offsetTop);height:expression(window.' + refName + '.cropRectangle.offsetHeight);width:expression(window.' + refName + '.cropRectangle.offsetLeft)';
			this.shadows.east.style.cssText = 'right:0px;position:absolute;top:expression(window.' + refName + '.cropRectangle.offsetTop);height:expression(window.' + refName + '.cropRectangle.offsetHeight);width:expression(window.' + refName + '.el.offsetWidth - (window.' + refName + '.cropRectangle.offsetWidth + window.' + refName + '.cropRectangle.offsetLeft))';
			this.shadows.north.style.cssText = 'left:0px;top:0px;width:100%;position:absolute;height:expression(window.' + refName + '.cropRectangle.offsetTop)';
			this.shadows.south.style.cssText = 'left:0px;bottom:0px;width:100%;position:absolute;height:expression(window.' + refName + '.el.offsetHeight - (window.' + refName + '.cropRectangle.offsetTop + window.' + refName + '.cropRectangle.offsetHeight))';
		}

	},

	_positionShadows : function() {
		if(!this.lazyShadows && Browser.Engine.name == 'trident') {
			this.fireEvent('crop', this);
			return;
		}
		var coordinates = this.getPreviewCoordinates();
		if(coordinates.left>0) {
			this.shadows.west.setStyles({
				top : coordinates.top,
				width : coordinates.left,
				height : coordinates.height,
				visibility : 'visible'
			});
		}else{
			this.shadows.west.setStyle('visibility','hidden');
		}

		var eastWidth = this.el.offsetWidth - (coordinates.width + coordinates.left);
		if(eastWidth>0) {
			this.shadows.east.setStyles({
				top : coordinates.top,
				width : eastWidth,
				height : coordinates.height,
				visibility : 'visible'
			});
		}else{
			this.shadows.east.setStyle('visibility','hidden');
		}

		if(coordinates.top > 0) {
			this.shadows.north.setStyles({
				height : coordinates.top,
				visibility : 'visible'
			});
		}else{
			this.shadows.north.setStyle('visibility','hidden');
		}

		var heightSouth = this.el.offsetHeight - (coordinates.top + coordinates.height);
		if(heightSouth > 0) {
			this.shadows.south.setStyles({
				height : heightSouth,
				visibility : 'visible'
			});
		}else{
			this.shadows.south.setStyle('visibility','hidden');
		}


	},
	_cropEvent : function() {
		this.fireEvent('crop', this);
	},
	_afterCrop : function() {
		this.fireEvent('aftercrop');
	},

	_beforeCrop : function() {
		this.fireEvent('beforecrop');
	},

	initEvents : function() {
		this.el.addEvent('selectstart', function() { return false; });
		this.el.addEvent('dragstart', function() { return false; });

		if(this.lazyShadows) {
			this.movable.addEvent('aftermove', this._positionShadows.bind(this));
			this.resizable.addEvent('afterresize', this._positionShadows.bind(this));
		}else{
			this.movable.addEvent('move', this._positionShadows.bind(this));
			this.resizable.addEvent('resize', this._positionShadows.bind(this));
		}

		this.movable.addEvent('move', this._cropEvent.bind(this));
		this.resizable.addEvent('resize', this._cropEvent.bind(this));

		this.movable.addEvent('aftermove', this._afterCrop.bind(this));
		this.resizable.addEvent('afterresize', this._afterCrop.bind(this));
		this.movable.addEvent('beforemove', this._beforeCrop.bind(this));
		this.resizable.addEvent('beforeresize', this._beforeCrop.bind(this));
		if(this.listeners) {
			this.addEvents(this.listeners);
		}


	},

	_createCropRectangle: function() {
		var div = this.cropRectangle = new Element('DIV');
		div.addClass('dg-crop');
		this.el.adopt(div);
		if(this.initialCoordinates) {
			div.setStyles(this._originalCoordinatesToPreview(this.initialCoordinates));
		}else{
			div.setStyles({
				left : 0,
				top : 0,
				width : this.el.offsetWidth - this._getFrameWidth(div),
				height: this.el.offsetHeight - this._getFrameHeight(div)
			});
		}
	},

	_createMovable : function() {
		this.movable = new DG.Movable(this.cropRectangle, $merge({
			boundaryEl : this.el,
			handleHeight : '100%'
		}, this.moveConfig));
	},

	_createResizable : function() {
		this.resizable = new DG.Resizable(this.cropRectangle, $merge({
			boundaryEl : this.el,
			handles : 'all',
			minWidth: 10,
			minHeight: 10,
			preserveRatio : this.preserveRatio
		},this.resizeConfig));
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

	},

	_originalCoordinatesToPreview : function(coordinates) {
		var ret = {};
		for(var key in coordinates) {
			var val = Math.round(coordinates[key] / this.ratio)
			if(key == 'width') {
				val -= this._getFrameWidth(this.cropRectangle);
			}
			if(key=='height') {
				val -= this._getFrameHeight(this.cropRectangle);
			}
			ret[key] = val;
		}
		return ret;
	},

	_setConfigProperties : function(config) {
		this.initialCoordinates = config.initialCoordinates || false;
		this.originalCoordinates = config.originalCoordinates || config.previewCoordinates;
		this.previewCoordinates = config.previewCoordinates;
		this.resizeConfig = config.resizeConfig ? $merge(this.resizeConfig, config.resizeConfig) : this.resizeConfig;
		this.moveConfig = config.moveConfig || {};
		this.ratio = this.originalCoordinates.width / this.previewCoordinates.width;
		this.listeners = config.listeners || null;
		if($defined(config.lazyShadows))this.lazyShadows = config.lazyShadows;

	},

	_getUnique : function() {
		var index = 0;
		while(window['crop' + index]) index++;
		window['crop' + index] = this;
		return 'crop' + index;
	}
});