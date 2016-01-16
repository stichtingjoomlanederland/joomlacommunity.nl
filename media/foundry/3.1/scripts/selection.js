(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

$.template("selection/box", '<div data-selection-box></div>');

$.Controller("Selection",
{
	pluginName: "selection",
	hostname: "selection",

	defaultOptions: {

		view: {
			box: "selection/box"
		},
		
		// Behaviour
		movable: true,
		selectable: true,
		spotlight: true,

		// Autodraw
		// Allow click to draw selection with default width & height.
		autodraw: true,
		autoWidth: 50,
		autoHeight: 50,
		tolerance: 15,

		// When null, it will try to take the value from css.
		// If a value is given, css is ignored.
		// minWidth/Height has higher precendence over autoWidth/autoHeight			
		minWidth: 0, 
		minHeight: 0,
		aspectRatio: null, // 1/1, 4/3, 16/9

		"{selection}": "[data-selection-box]"
	}
},
function(self) { return {

	init: function() {
	},

	disabled: false,

	drawing: false,

	createSelection: function() {

		// Remove any existing selection
		self.removeSelection();

		// Create new box
		var selection = 
			self.view.box()
				.appendTo(self.element);

		self.trigger("selectionCreate", [selection, self]);
	},

	removeSelection: function() {

		var selection = self.selection();

		if (selection.length < 1) return;

		self.selection().remove();

		self.trigger("selectionRemove", [self]);
	},

	coords: function(event) {

		var coords   = [event.pageX, event.pageY];
			coords.x = event.pageX;
			coords.y = event.pageY;

		return coords;
	},

	offset: function() {

		var selection = self.selection(),
			offset    = selection.offset();

			offset.top    = offset.top  + parseInt(selection.css("borderTopWidth"));
			offset.left   = offset.left + parseInt(selection.css("borderLeftWidth"));
			offset.bottom = offset.top  + selection.height();
			offset.right  = offset.left + selection.width();

		return offset;
	},

	calculateArea: function(collision, offset) {

		// Normalize arguments
		if (!collision) { collision = "clip" };
		if (!offset)    { offset = {x: 0, y: 0} };

		// Options
		var options     = self.options,
			autoWidth   = options.autoWidth,
			autoHeight  = options.autoHeight,
			minWidth    = options.minWidth,
			minHeight   = options.minHeight,
			aspectRatio = options.aspectRatio;			
		
		// Calculate image area
		var viewportEl      = self.element,
			viewport        = viewportEl.offset();
			viewport.width  = viewportEl.width();
			viewport.height = viewportEl.height();
			viewport.right  = viewport.width  + viewport.left;
			viewport.bottom = viewport.height + viewport.top;

		// Calculate selection's dimension
		// width, height
		var area = self.area;
		area.width  = Math.abs(area.endX - area.startX);
		area.height = Math.abs(area.endY - area.startY);

		// Determine if we need to autodraw
		var tolerance   = options.tolerance,
			autodraw    = options.autodraw && (area.width < tolerance && area.height < tolerance);

		// Readjust selection's width/height again, this time taking account of
		// autoWidth/autoHeight, minWidth/minHeight & aspectRatio constrains.
		area.width  = Math.max(area.width , minWidth , ((autodraw) ? autoWidth  : 0));
		area.height = Math.max(area.height, minHeight, ((autodraw) ? autoHeight : 0));

		// Aspect ratio correction
		if (aspectRatio) { area.width = area.height * aspectRatio; }

		// If we're autodrawing selection,
		// this ensures selection appears at the center of the cursor
		if (autodraw) {

			area.endX = area.startX + area.width;
			area.endY = area.startY + area.height;

			collision = "flip";

			offset = {
				x: area.width / -2,
				y: area.height / -2
			}
		}

		// Calculate selection's position
		// top, left, right, bottom
		area.top    = ((area.startY <= area.endY) ? area.startY : area.endY) + offset.y;
		area.left   = ((area.startX <= area.endX) ? area.startX : area.endX) + offset.x;
		area.right  = area.width  + area.left;
		area.bottom = area.height + area.top;

		// Collision handling
		if (collision=="clip") {

			// Cap area within image boundaries
			if (area.top    <= viewport.top   ) {area.top    = viewport.top;   }
			if (area.bottom >= viewport.bottom) {area.bottom = viewport.bottom;}
			if (area.left   <= viewport.left  ) {area.left   = viewport.left;  }
			if (area.right  >= viewport.right ) {area.right  = viewport.right; }

			// Resize tag
			area.width  = area.right  - area.left;
			area.height = area.bottom - area.top;

			// Aspect ratio correction
			if (aspectRatio) {

				// If width correction results in bleeding
				if ((area.left + area.height * aspectRatio) > viewport.right) {
					// correct area height
					area.height = area.width / aspectRatio;
				} else {
					area.width = area.height * aspectRatio;
				}

				// Ensure they don't go out of bounds
				area.bottom = area.height + area.top;
				area.right  = area.width  + area.left;
			}
		}

		// Reposition tag
		if (collision=="flip") {

			if (area.top <= viewport.top) {
				area.top = viewport.top;
				area.bottom = area.height + area.top;
			}

			if (area.left <= viewport.left) {
				area.left = viewport.left;
				area.right  = area.width  + area.left;
			}

			if (area.right >= viewport.right) {
				area.right = viewport.right;
				area.left  = area.right - area.width;
			}

			if (area.bottom >= viewport.bottom) {
				area.bottom = viewport.bottom;
				area.top    = area.bottom - area.height;
			}
		}

		// Offset relative to document
		area.offset = {
			top: area.startY,
			left: area.startX, 
			bottom: area.startY + area.height,
			right: area.startX + area.width
		};

		// Pixel unit
		area.pixel = {
			top   : area.top  - viewport.top,
			left  : area.left - viewport.left,
			width : area.width,
			height: area.height
		};

		// Decimal unit
		area.decimal = {
			top   : area.pixel.top  / viewport.height,
			left  : area.pixel.left / viewport.width,
			width : area.width      / viewport.width,
			height: area.height     / viewport.height
		}

		// Percentage unit
		area.percentage = {
			top   : (area.decimal.top    * 100) + "%",
			left  : (area.decimal.left   * 100) + "%",
			width : (area.decimal.width  * 100) + "%",
			height: (area.decimal.height * 100) + "%"
		};

		// Also add a reference to the viewport
		area.viewport = viewport;

		return area;
	},

	set: function(type, prop, coords) {

		var type = self[type] || (self[type]={});
			type[prop + "X"] = coords.x;
			type[prop + "Y"] = coords.y;
	},

	drawSelection: function() {

		var area = self.calculateArea(),
			selection = self.selection().css(area.percentage);

		self.trigger("selectionChange", [area, selection, self]);

		return area;
	},

	"{self} mousedown": function(viewport, event) {

		var options = self.options;

		// If we're disabled or this is not selectable, stop.
		if (self.disabled || !options.selectable) return;
		
		// If we can move selection,
		if (options.movable &&

			// and the selection area was the event target,
			event.target == self.selection()[0] &&

			// and the clicked area was the spotlight area, stop.
			$.intersects(self.offset(), self.coords(event))) return;

		// Prevent browser drag & drop behaviour
		event.preventDefault();

		// If no selection has been created yet, create one first.
		if (self.selection().length < 1) {
			self.createSelection();
		}

		// Set start x, y
		self.drawing = true;
		self.set("area", "start", self.coords(event));

		$(document)

			// Set current x, y
			.on("mousemove.selection.selecting", function(event) {
				if (!self.drawing) return;
				self.set("area", "end", self.coords(event));
				self.drawSelection();
			})

			// Set end x, y
			.on("mouseup.selection.selecting", function(event) {
				self.set("area", "end", self.coords(event));
				self.drawSelection();
				$(document).off("mousemove.selection.selecting mouseup.selection.selecting");
			});
	},

	moveSelection: function(offset) {

		// Calculate area relative to screen
		var anchor = self.anchor,

			dx = anchor.startX - anchor.endX,
			dy = anchor.startY - anchor.endY,

			area = self.calculateArea("flip", {x: dx * -1, y: dy * -1}),

			selection = self.selection().css(area.percentage);

		self.trigger("selectionChange", [area, selection, self]);

		return area;
	},

	"{selection} mousedown": function(selection, event) {

		var options = self.options;

		// If we're disabled or this is not movable, stop.
		if (self.disabled || !options.movable) return;

		// If user did not click on the spotlight area, stop.
		if (!$.intersects(self.offset(), self.coords(event))) return;

		// If user did not intend to click on selection, stop.
		if (event.target!==selection[0]) return;
		
		// Prevent browser drag & drop behaviour
		event.preventDefault();

		// Disable autodraw when moving selection
		var autodraw = options.autodraw;
			options.autodraw = false;

		self.drawing = true;
		self.set("anchor", "start", self.coords(event));

		$(document)
			.on("mousemove.selection.moving", function(event) {

				if (!self.drawing) return;

				self.set("anchor", "end", self.coords(event));
				self.moveSelection();
			})
			.on("mouseup.selection.moving", function(event) {

				self.set("anchor", "end", self.coords(event));
				var area = self.moveSelection();

				// Reset start end pivot values
				// This ensure move selection doesn't snap off to other places.
				self.set("area", "start", {x: area.left , y: area.top   });
				self.set("area", "end"  , {x: area.right, y: area.bottom});	

				$(document).off("mousemove.selection.moving mouseup.selection.moving");

				// Restore autodraw
				options.autodraw = autodraw;
			});
	},

	"{self} selectionChange": function(el, event, area, selection) {

		if (self.options.spotlight) {

			var viewport = area.viewport,
				pixel = area.pixel;

			selection.css({
				marginTop         : pixel.top * -1,
				marginLeft        : pixel.left * -1,
				borderTopWidth    : pixel.top,
				borderLeftWidth   : pixel.left,
				borderBottomWidth : viewport.bottom - area.bottom,
				borderRightWidth  : viewport.right - area.right
			});
		}
	},

	"{window} mousemove": function(el, event) {

		var selection = self.selection();

		if (selection.length < 1) return;

		// Switch cursor between crosshair & move
		if (self.options.spotlight) {
			self.selection()
				.css("cursor", $.intersects(self.offset(), self.coords(event)) ? "move" : "crosshair");
		}
	},

	"{window} keydown": function(el, event) {

		if (event.keyCode==27) {
			self.removeSelection();
		}
	}

}});
}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD31.module("selection", moduleFactory);

}());