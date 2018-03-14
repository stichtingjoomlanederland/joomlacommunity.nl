/*
---
description: A non-obtrusive image dropdown menu that extends and replaces a standard HTML Select control. 

license: MIT-style

authors:
- Lorenzo Stanco

requires:
- core/1.4.1: '*'

provides: [FancySelect]

...
*/

var FancySelect = new Class({

	Implements: [Options, Events],

	options: {
		legacyEvents: false,
		showText: true,
		showColors: true,
		className: 'fancy-select',
		offset: { x: 0, y: 0 },
		autoHide: false,
		autoScrollWindow: false,
		animateFade: true,
		animateSlide: true,
		fx: { 'duration': 'short' }
	},

	initialize: function(element, options) {
	
		this.setOptions(options);
		/*if (!Fx.Slide)*/ this.options.animateSlide = false; // Need review
		this.element = document.id(element);
		this.element.store('fancyselect_object', this);
		this._create();
		this.attach();
		
		// Auto-scroll when FancySelect is out of viewport
		if (this.options.autoScrollWindow) this.addEvent('show', function() {
			var windowScroll = window.getScroll();
			var overflow = this.ul.getPosition().y + this.ul.getSize().y - window.getSize().y - windowScroll.y;
			if (overflow > 0) window.scrollTo(windowScroll.x, windowScroll.y + overflow + 10);
		});
		
		// Auto-hide the dropdown menu when user clicks outside
		if (this.options.autoHide) document.addEvent('click', function(e) {
			if (!this.shown) return;
			var target = document.id(e.target);
			var parents = target.getParents().include(target);
			if (!parents.contains(this.ul) && !parents.contains(this.div)) this.hide();
		}.bind(this));
		
		return this;
		
	},

	attach: function() {
		this.element.setStyle('display', 'none');
		this.select(this.element.get('value')); // Select current item
		if (Browser.ie) window.addEvent('load', function() { this.select(this.element.get('value')); }.bind(this)); // IE refresh fix
		this.ul.fade('hide').inject(document.id(document.body));
		this.div.inject(this.element, 'after');
		this.attached = true;
		this.fireEvent('attach');
		return this;
	},

	detach: function() {
		if (this.ul) this.ul.dispose();
		if (this.div) this.div.dispose();
		this.element.setStyle('display', '');
		this.attached = false;
		this.fireEvent('detach');
		return this;
	},
	
	select: function(value) {
		// Update hidden <select>
		if (this.element.get('value') != value) {
			this.element.set('value', value);
			
			// Throw "change" event
			if (this.options.legacyEvents) {
				this.element.fireEvent('change');
				this.element.getParents().fireEvent('change');
			}
			
		}
		
		if (this.options.showText) this.div.getElement('span.textSelected').set('text', this.selectOptions[this.tableOrdering[value]].text);
		if (this.options.showColors) this.div.getElement('span.colorSelected').setStyles({ background: this.selectOptions[this.tableOrdering[value]].color, border: '2px solid '+this.selectOptions[this.tableOrdering[value]].color });
		if (this.ul) {
			this.ul.getElements('li').each(function(li) {
				if (li.getProperty('data-value') == value) li.addClass('selected');
				else li.removeClass('selected');
			});
		}
		return this;
	},
	
	update: function() {
		var attached = this.attached;
		this.detach();
		this._create(); // Re-create
		if (attached) this.attach(); // Re-attach if needed
		return this;
	},

	show: function() {
		var offset = this.options.offset;
		var position = this.div.getCoordinates();
		this.ul.setStyles({
			'top': position.top + position.height + offset.y,
			'left': position.left + offset.x });
		this._animate(false);
		this.shown = true;
		this.fireEvent('show');
		return this;
	},
	
	hide: function() {
		this._animate(true);
		this.shown = false;
		this.fireEvent('hide');
		return this;
	},
	
	toggle: function() {
		if (this.shown) return this.hide();
		else return this.show();
	},
	
	_create: function() {
		
		var o = this.options;
		
		if (this.ul) this.ul.destroy();
		if (this.div) this.div.destroy();
		
		// Create options array
		this.selectOptions = {};
		this.tableOrdering = {};
		var x = 0;
		this.element.getElements('option').each(function(option) {
			var value = option.getProperty('value');
			this.tableOrdering[value] = x;
			this.selectOptions[x] = {};
			if (option.get('disabled')) this.selectOptions[x].disabled = true;
			if (o.showText) this.selectOptions[x].text = option.get('text');
			if (o.showColors) {
				this.selectOptions[x].color = option.getProperty('data-color');
			}
			this.selectOptions[x].value = value;
			x++;
		}.bind(this));
		
		// Create <li> elements
		this.ul = new Element('ul').addClass(o.className);
		var count = 0;
		
		Object.each(this.selectOptions, function(option, value) {
			
			var li = new Element('li', { 'data-value': option.value }).addClass('hasElement');
			if (option.disabled) li.addClass('disabled');
			
			if (o.showColors) {
				if (option.color) {
					var theelement = new Element('span.color').setStyles({ background: option.color, border: '2px solid '+option.color });
				} else {
					var theelement = new Element('span.color');
				}
				li.adopt(theelement);
			}
			
			if (o.showText && option.text) li.adopt(new Element('span.text', { 'text': option.text }));
			li.addEvent('click', function() { 
				if (li.hasClass('disabled')) return;
				this.select(li.getProperty('data-value'));
				this.hide();
				if (li.getProperty('data-value') == '')
					rs_calendar_add_filter('');
				else
					rs_calendar_add_filter(option.text);
			}.bind(this));
			this.ul.adopt(li);
			
			count++;
			if (count == 3) {
				this.ul.adopt(new Element('li').addClass('hide'));
				count = 0;
			}
		}.bind(this));
		
		if (count != 0 && count != 3) {
			toadd = 3 - count;
			
			for (k=0;k<toadd;k++) {
				var li = new Element('li').addClass('hasElement');
				li.adopt(new Element('span.color'));
				li.adopt(new Element('span.text'));
				
				this.ul.adopt(li);
			}
		}
		
		// Force <ul> custom positioning
		this.ul.setStyles({ position: 'absolute', top: 0, left: 0 });
		if (o.animateFade) this.ul.set('tween', o.fx);
		if (o.animateSlide) this.ul.set('slide', o.fx);
		
		// Create <div> replacement for select
		this.div = new Element('div').addClass(o.className);
		if (o.showColors) this.div.adopt(new Element('span.colorSelected'));
		if (o.showText) this.div.adopt(new Element('span.textSelected'));
		this.div.adopt(new Element('span.arrow'));
		this.div.addEvent('click', function() { this.toggle(); }.bind(this));
		
		return this;
		
	},
	
	_animate: function(out) {
		var o = this.options;
		if (o.animateFade) this.ul.fade(out ? 'out' : 'in');
		if (o.animateSlide) this.ul.slide(out ? 'out' : 'in');
		if (!o.animateFade && !o.animateSlide) this.ul.fade(out ? 'hide' : 'show');
		return this;
	}
	
});

Elements.implement({
	
	fancySelect: function(options) {
		this.each(function(el) { new FancySelect(el, options); });
		return this;
	}
	
});

Element.implement({
	
	fancySelect: function(options) {
		new FancySelect(document.id(this), options);
		return this;
	},
	
	fancySelectShow: function() {
		var fs = this.retrieve('fancyselect_object');
		if (fs) fs.show(this);
		return this;
	},
	
	fancySelectHide: function() {
		var fs = this.retrieve('fancyselect_object');
		if (fs) fs.hide(this);
		return this;
	},
	
	fancySelectToggle: function() {
		var fs = this.retrieve('fancyselect_object');
		if (fs) fs.toggle(this);
		return this;
	}
	
});

Element.Properties.fancySelect = {
 
    get: function() {
        return this.retrieve('fancyselect_object');
    }
 
};
